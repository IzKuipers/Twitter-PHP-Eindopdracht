<?php
require_once ("connectie.php");

session_start(); // Start de sessie als het bestand wordt geïmporteerd

// Deze functie geeft een succesmelding weer als iets is gelukt
function geefSuccesWeer()
{
  // Als er geen succesmelding is, doe dan ook niks.
  if (!isset($_SESSION["succes"])) {
    return;
  }

  $id = $_SESSION["succes"]; // De ID van de succesmelding
  unset($_SESSION["succes"]); // We hebben de succes-ID nu gebruikt, dus hebben we hem niet meer nodig.

  // Verbind met de database, maar gooi geen succesmelding als de connectie is mislukt ($geefFoutmelding == false)
  $connectie = verbindMysqli(false);

  $icon = "check_circle";
  $melding = ""; // Melding van de dialoog

  try { // Probeer...
    global $melding; // Globalen voor het weergeven van de HTML en het sluiten van de statement

    if (!$connectie) {
      // Connectie mislukt: gooi een foutmelding (handmatige implementatie van $geefFoutmelding == true in verbindMysqli())
      throw new Exception("Connectie met server mislukt");
    }

    // De vraag aan de database: Geef mij alle succesmeldingen wiens de ID (id = ?) gelijk staat aan ?
    $query = "SELECT * FROM succes WHERE id = ?";

    $statement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
    $statement->execute(); // Voer de statement uit

    $statement->bind_result($id, $melding, $icon); // Schrijf het resultaat naar de respectieve variabelen. De volgorde is hetzelfde als in de tabel.
    $statement->fetch(); // We hoeven fetch() maar eenmalig op te roepen omdat ID's toch uniek zijn, een while loop is hier overbodig.

    // De $melding variabele is NULL omdat de succesmelding niet bestaat
    if (!$melding) {
      $melding = "Gelukt";
    }
  } catch (Exception $e) { // We konden de succesmelding niet uit de database halen, dus is er echt iets goed mis met de verbinding.
    $melding = "Gelukt";
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten via sluitMysqli()
    sluitMysqli($connectie, $statement);
  }

  // Geef de uiteindelijke HTML weer van de succesmelding. Opmerkelijk hier is de CSS import. Deze import zorgt ervoor dat:
  // 1) we alleen de CSS voor de succesmelding importeren als er ook echt een succesmelding is, en
  // 2) dat de CSS voor de succesmelding altijd ingeladen is als de succesmelding er _wel_ is.
  echo <<<HTML
      <script defer>
        document.addEventListener("DOMContentLoaded", () => {
          const wrapper = document.getElementById("succesWrapper");

          if (!wrapper) return;

          setTimeout(() => {
            wrapper.children[0].classList.add("visible");
          }, 100);

          setTimeout(() => {
            wrapper.children[0].classList.remove("visible");
          }, 3100);
        });
      </script>
    <link rel="stylesheet" href="/css/succes.css">
    <div class="succes-wrapper" id="succesWrapper">
      <div class="succes">
        <span class="material-icons-round">$icon</span>
        <span class="bericht">$melding</span>
      </div>
    </div>
  HTML;
}