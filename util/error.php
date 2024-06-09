<?php
require_once ("connectie.php");

session_start(); // Start de sessie als het bestand wordt geïmporteerd

function foutmelding(int $id, string $continue = "", string $message = "")
{
  $aanvraagUri = $_SERVER["REQUEST_URI"]; // Dit is de Uniform Resource Identifier, in feiten gewoon de URL van de pagina, inclusief de GET parameters en Hash
  $huidigeUrl = parse_url($aanvraagUri); // Verkrijg de individuele onderdelen van de URI in vorm van een associative array (URL pad als "path" en de GET parameters als "query")
  $paginaPad = $huidigeUrl['path']; // Het pad van de huidige PHP pagina

  $_SESSION["error_message"] = $message; // Zet de eventuele technische informatie in de session voor wanneer de foutmelding wordt weergegeven met geef_foutmelding_weer()

  // Voeg de fout-ID en "continue" (de locatie van de 'Sluiten' knop) toe aan de GET parameters.
  // Enige POST wordt hier weggegooid, maar dat is dan toch niet meer relevant.
  header("location: $paginaPad?error=$id&continue=$continue");
}

// Deze foutmelding maakt de daadwerkelijke foutmelding die aan de gebruiker wordt weergegeven.
// Dankzij deze functie kan er op iedere pagina op dezelfde manier een foutmelding worden weergegeven.
function geef_foutmelding_weer()
{
  // Als er geen foutmelding is, doe dan ook niks.
  if (!isset($_GET["error"], $_GET["continue"])) {
    unset($_SESSION["error_message"]);
    return;
  }

  $id = $_GET["error"]; // De ID van de foutmelding
  $continue = $_GET["continue"]; // De href van de 'Sluiten'-knop

  // Verbind met de database, maar gooi geen foutmelding als de connectie is mislukt ($geef_foutmelding == false)
  $connectie = verbind_mysqli(false);

  $titel = ""; // Titel van de dialoog
  $foutmelding = ""; // Foutmelding van de dialoog
  $details = (isset($_SESSION["error_message"]) ? $_SESSION["error_message"] : "(geen)"); // De technische informatie, met een ternary operator om te checken of die informatie ook echt is meegestuurd met de foutmelding

  // We hebben de error_message nu gebruikt, dus eet hem op omdat we hem niet meer nodig hebben.
  unset($_SESSION["error_message"]);

  try { // Probeer...
    global $statement, $titel, $foutmelding; // Globalen voor het weergeven van de HTML en het sluiten van de statement

    if (!$connectie) {
      // Connectie mislukt: gooi een foutmelding (handmatige implementatie van $geef_foutmelding == true in verbind_mysqli())
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
    // Probeer de connectie en statement te sluiten via sluit_mysqli()
    sluit_mysqli($connectie, $statement);
  }

  // Geef de uiteindelijke HTML weer van de foutmelding. Opmerkelijk hier is de CSS import. Deze import zorgt ervoor dat:
  // 1) we alleen de CSS voor de foutmelding importeren als er ook echt een foutmelding is, en
  // 2) dat de CSS voor de foutmelding altijd ingeladen is.
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
function we_zijn_offline()
{
  header("location:/offline.php"); // Stuur de gebruiker naar de offline-pagina
}