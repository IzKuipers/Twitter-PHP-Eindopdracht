<?php
require_once ("connectie.php");
require_once ("toast.php");

function foutmelding(Foutmeldingen $id, string $continue = "", string $message = "")
{
  session_start(); // Start de sessie voor fout_details

  $aanvraagUri = $_SERVER["REQUEST_URI"]; // Dit is de Uniform Resource Identifier, in feiten gewoon de URL van de pagina, inclusief de GET parameters en Hash
  $huidigeUrl = parse_url($aanvraagUri); // Verkrijg de individuele onderdelen van de URI in vorm van een associative array (URL pad als "path" en de GET parameters als "query")
  $paginaPad = $huidigeUrl['path']; // Het pad van de huidige PHP pagina

  $_SESSION["fout_details"] = $message; // Zet de eventuele technische informatie in de session voor wanneer de foutmelding wordt weergegeven met geefFoutmeldingWeer()

  $id = $id->value;

  // Voeg de fout-ID en "continue" (de locatie van de 'Sluiten' knop) toe aan de GET parameters.
  // Enige POST wordt hier weggegooid, maar dat is dan toch niet meer relevant.
  header("location: $paginaPad?error=$id&continue=$continue");
}

// Deze foutmelding maakt de daadwerkelijke foutmelding die aan de gebruiker wordt weergegeven.
// Dankzij deze functie kan er op iedere pagina op dezelfde manier een foutmelding worden weergegeven.
function geefFoutmeldingWeer()
{
  session_start(); // Start de sessie voor fout_details
  geefToastWeer(); // laat een eventuele toast zien als deze er is

  // Als er geen foutmelding is, doe dan ook niks.
  if (!isset($_GET["error"], $_GET["continue"])) {
    unset($_SESSION["fout_details"]);
    return;
  }

  $id = $_GET["error"]; // De ID van de foutmelding
  $continue = $_GET["continue"]; // De href van de 'Sluiten'-knop

  // Verbind met de database, maar gooi geen foutmelding als de connectie is mislukt ($geef_foutmelding == false)
  $connectie = verbindMysqli(false);

  $titel = ""; // Titel van de dialoog
  $foutmelding = ""; // Foutmelding van de dialoog
  $details = (isset($_SESSION["fout_details"]) ? $_SESSION["fout_details"] : "(geen)"); // De technische informatie, met een ternary operator om te checken of die informatie ook echt is meegestuurd met de foutmelding

  // We hebben de fout_details nu gebruikt, dus eet hem op omdat we hem niet meer nodig hebben.
  unset($_SESSION["fout_details"]);

  try { // Probeer...
    if (!$connectie) {
      // Connectie mislukt: gooi een foutmelding (handmatige implementatie van $geefFoutmelding == true in verbindMysqli())
      throw new Exception("Connectie met server mislukt");
    }

    // De vraag aan de database: Geef mij alle foutmeldingen wiens de ID (id = ?) gelijk staat aan ?
    $query = "SELECT * FROM errors WHERE id = ?";

    $statement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
    $statement->execute(); // Voer de statement uit

    $statement->bind_result($id, $titel, $foutmelding); // Schrijf het resultaat naar de respectieve variabelen. De volgorde is hetzelfde als in de tabel.
    $statement->fetch(); // We hoeven fetch() maar eenmalig op te roepen omdat ID's toch uniek zijn, een while loop is hier overbodig.

    // De $titel variabele is NULL omdat de foutmelding niet bestaat
    if (!$titel) {
      $titel = "Onbekende fout";
      $foutmelding = "Er is een fout opgetreden die niet bekend is. De details geven mogelijk meer informatie over de fout. Onze excuses voor het ongemak.";
    }
  } catch (Exception $e) { // We konden de foutmelding niet uit de database halen, dus is er echt iets goed mis met de verbinding.
    $titel = "Dubbele fout!";
    $foutmelding = "Foutcode $id is opgetreden, maar het is niet gelukt om met de database te verbinden om de details van de foutmelding op te vragen. Onze excuses voor het ongemak.";
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten via sluitMysqli()
    sluitMysqli($connectie, $statement);
  }

  // Geef de uiteindelijke HTML weer van de foutmelding. Opmerkelijk hier is de CSS import. Deze import zorgt ervoor dat:
  // 1) we alleen de CSS voor de foutmelding importeren als er ook echt een foutmelding is, en
  // 2) dat de CSS voor de foutmelding altijd ingeladen is als de foutmelding er _wel_ is.
  echo <<<HTML
    <link rel="stylesheet" href="/css/error.css">
    <div class="error-wrapper">
      <div class="error">
        <div class="content">
          <h1>$titel</h1>
          <p>$foutmelding</p>
          <p class="details">Details: <span>$details</span></p>
        </div>
        <div class="bottom">
          <a href="$continue">Sluiten</a>
        </div>
      </div>
    </div>
  HTML;
}

// Deze functie wordt aangeroepen als de database verbinding mislukt
function weZijnOffline()
{
  header("location:/offline.php"); // Stuur de gebruiker naar de offline-pagina
}

function weZijnMisschienOffline()
{
  $online = isDatabaseOnline();

  if (!$online) {
    weZijnOffline();

    die;
  }
}

// Ik gebruik een enumeration om de foutcodes op een centrale
// manier te noteren om ze gemakkelijk te kunnen veranderen.
// 
// Deze namen staan als het goed is gelijk aan de foutmeldingen
// in de tabel eindopdracht.errors
enum Foutmeldingen: int
{
  case GebruikerNietGevonden = 1;
  case WachtwoordOnjuist = 2;
  case VerbindingMislukt = 3;
  case WachtwoordenMismatch = 4;
  case GebruikerBestaatAl = 5;
  case ControleMislukt = 6;
  case PostLikeMislukt = 7;
  case VersturenMislukt = 8;
  case StatusUpdateMislukt = 9;
}