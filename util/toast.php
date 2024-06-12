<?php
require_once ("connectie.php");

// Deze functie geeft een toastmelding weer als iets is gelukt
function geefToastWeer()
{
  // Als er geen toastmelding is, doe dan ook niks.
  if (!isset($_SESSION["toast"])) {
    return;
  }

  $id = $_SESSION["toast"]; // De ID van de toastmelding
  unset($_SESSION["toast"]); // We hebben de toast-ID nu gebruikt, dus hebben we hem niet meer nodig.

  // Verbind met de database, maar gooi geen toastmelding als de connectie is mislukt ($geefFoutmelding == false)
  $connectie = verbindMysqli(false);

  $icoon = "check_circle";
  $bericht = ""; // Bericht van de toast

  try { // Probeer...
    if (!$connectie) {
      // Connectie mislukt: gooi een foutmelding (handmatige implementatie van $geefFoutmelding == true in verbindMysqli())
      throw new Exception("Connectie met server mislukt");
    }

    // De vraag aan de database: Geef mij alle toastmeldingen wiens de ID (id = ?) gelijk staat aan ?
    $query = "SELECT * FROM toast WHERE id = ?";

    $statement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
    $statement->execute(); // Voer de statement uit

    $statement->bind_result($id, $bericht, $icoon, $type); // Schrijf het resultaat naar de respectieve variabelen. De volgorde is hetzelfde als in de tabel.
    $statement->fetch(); // We hoeven fetch() maar eenmalig op te roepen omdat ID's toch uniek zijn, een while loop is hier overbodig.

    // De $bericht variabele is NULL omdat de toastmelding niet bestaat
    if (!$bericht) {
      $bericht = "Gelukt";
    }
  } catch (Exception $e) { // We konden de toastmelding niet uit de database halen, dus is er echt iets goed mis met de verbinding.
    $bericht = $e->getMessage();
    $type = "fout";
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten via sluitMysqli()
    sluitMysqli($connectie, $statement);
  }

  // Geef de uiteindelijke HTML weer van de toastmelding. Opmerkelijk hier is de CSS import. Deze import zorgt ervoor dat:
  // 1) we alleen de CSS voor de toastmelding importeren als er ook echt een toastmelding is, en
  // 2) dat de CSS voor de toastmelding altijd ingeladen is als de toastmelding er _wel_ is.
  echo <<<HTML
      <script defer>
        document.addEventListener("DOMContentLoaded", () => {
          const wrapper = document.getElementById("toastWrapper");

          if (!wrapper) return;

          setTimeout(() => {
            wrapper.children[0].classList.add("visible");
          }, 100);

          setTimeout(() => {
            wrapper.children[0].classList.remove("visible");
          }, 3100);
        });
      </script>
      <link rel="stylesheet" href="/css/toast.css">
      <div class="toast-wrapper" id="toastWrapper">
        <div class="toast $type">
          <span class="material-icons-round">$icoon</span>
          <span class="bericht">$bericht</span>
        </div>
      </div>
  HTML;
}