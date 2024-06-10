<?php

// Importeer de benodigde functies voor deze pagina
require_once ("./util/connectie.php");
require_once ("./util/session.php");
require_once ("./util/error.php");
require_once ("./util/posts.php");
require_once ("./ui/headerbar.php");

session_start(); // Start de sessie
verifieerIngelogd(); // Controleer of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een eventuele foutmelding weer

$gebruiker = gebruikerUitSessie(); // Haal de gebruiker uit de sessie

// Check of er een gebruiker-ID zit in de GET data
if (!isset($_GET["id"])) {
  header("location:/"); // Geen gebruiker-ID, terug naar de index pagina
}

$id = $_GET["id"]; // De ID van de gebruiker
$posts = postsVanGebruiker($id); // De posts van deze gebruiker
$auteur = gebruikerOphalen($id); // De gebruiker van de ID

// Controleer of de gebruiker bestaat
if (!$auteur["naam"]) {
  header("location:/"); // Gebruiker bestaat niet, terug naar de index pagina
}

// De status van de auteur
$status = $auteur["status"];
              
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/profile.css">
  <title><?= $auteur["naam"] ?> - Twitter</title>
  <script defer>
    // Deze functie wordt gebruikt om de status te kunnen veranderen
    function toggleUpdate(e) {
      const input = document.getElementById("statusDisplay"); // De input
      const knop = document.getElementById("statusUpdateKnop"); // De knop voor opslaan
      const submit = document.getElementById("formSubmitKnop") // De verborgen submit-knop

      e.preventDefault(); // Stop de standaard events
      e.stopPropagation(); // Stop propagatie
      e.stopImmediatePropagation(); // Stop spontane propagatie

      // Check of de input op dit moment alleen-lezen is
      if (input.getAttribute("readonly")) {
        input.removeAttribute("readonly"); // Maak de input niet langer read only
        knop.innerText = "check"; // Verander de knop naar een checkmark

        return;
      }

      submit.click(); // Klik op de submit knop om de wijzigingen op te sturen.
    }

    onload = function() { // onload wordt aangeroepen als de DOM klaar is met laden
      const knop = document.getElementById("statusUpdateKnop"); // De knop voor opslaan
      knop.addEventListener("click", (e) => toggleUpdate(e)); // Voeg een event klik-event toe om de functie uit te voeren als er op de knop wordt geklikt 
    }
    
  </script>
</head>

<body>
  <!-- De headerbar van de pagina -->
  <?php HeaderBar($gebruiker) ?>
  <!-- De content van de pagina-->
  <main>
    <!-- Het profiel-stuk van de pagina-->
    <div class="profile">
      <!-- De blauwe achtergrond van de profiel-pagina -->
      <div class="top"></div>
      <div class="bottom">
        <!-- De ID van de gebruiker -->
        <span class="id">#<?= $id ?></span>
        <!-- Een standaard profielfoto -->
        <img src="/images/pfp.png" class="pfp">
        <!-- De naam van de auteur -->
        <h1 class="username"><?= $auteur["naam"] ?></h1>
        <form action="/updatestatus.php" method="POST">
          <p class="status">
            <input type="text" name="status" value="<?=$status?>" id="statusDisplay" readonly="true" maxlength="50">
            <?php 
              
            // Controleer of de ingelogde gebruiker de auteur is en geef de status-bewerk-knop weer als dat zo is
            if ($id == $gebruiker["id"]) {
              echo <<<HTML
                <button class="material-icons-round" id="statusUpdateKnop">edit</button>
                <input type="submit" value="submit" id="formSubmitKnop">  
              HTML;
            }
            ?>
        </p>
        </form>
      </div>
    </div>
    <!-- Geef de posts van de auteur weer -->
    <?php postLijst($posts) ?>
  </main>
</body>

</html>