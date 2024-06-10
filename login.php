<?php

require_once ("./util/error.php");
require_once ("./util/session.php");

session_start(); // Start de sessie
geefFoutmeldingWeer(); // Geef de eventuele foutmelding weer

// Deze functie wordt gebruikt om de gebruiker in te loggen via de POST data
function gebruikerInloggen()
{
  // Check via de request method en POST data of de gebruiker probeert in te loggen
  if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST["gebruikersnaam"], $_POST["wachtwoord"])) {
    return; // Gebruiker probeert niet in te loggen, stop.
  }

  $gebruikersnaam = $_POST["gebruikersnaam"]; // De gebruikersnaam die de gebruiker heeft ingevoerd
  $wachtwoord = $_POST["wachtwoord"]; // Het wachtwoord die de gebruiker heeft ingevoerd

  // Maak verbinding met de database
  $connectie = verbindMysqli();

  if (!$connectie) {
    weZijnOffline(); // Connectie mislukt, naar de offline pagina dan maar!
    return;
  }


  try { // Probeer...
    global $loginSelectStatement; // Maak de statement globaal om deze later te kunnen sluiten

    // De vraag aan de database: Geef mij de ID, de wachtwoord-hash en de status van alle gebruikers wiens naam gelijk staat aan ?
    $query = "SELECT idGebruiker,wachtwoord,status FROM gebruikers WHERE naam=?";

    $loginSelectStatement = $connectie->prepare($query); // Bereid de vraag voor
    $loginSelectStatement->bind_param("s", $gebruikersnaam); // Vervang het vraagteken met de daadwerkelijke gebruikersnaam
    $loginSelectStatement->execute(); // Voer de vraag uit
    $loginSelectStatement->bind_result($idGebruiker, $wachtwoordHash, $status); // Schrijf het resultaat naar de respectieve variabelen
    $loginSelectStatement->fetch(); // Vraag het resultaat op. Dit hoeft maar eenmalig te gebeuren omdat ID's uniek zijn

    // Controleer of de gebruiker bestaat
    if (!$idGebruiker) {
      foutmelding(1, "/login.php", "idGebruiker was NULL"); // Gebruiker bestaat niet, geef een foutmelding weer

      return;
    }

    // Controleer of het wachtwoord klopt met de builtin hashing-functie password_verify()
    $wachtwoordKlopt = password_verify($wachtwoord, $wachtwoordHash);

    if (!$wachtwoordKlopt) {
      foutmelding(2, "/login.php", "Wachtwoord fout"); // Het wachtwoord klopt niet, geef een foutmelding weer

      return;
    }

    // Schrijf de eigenschappen van de gebruiker naar de session om later te gebruiken
    $_SESSION["gebruiker"] = array("naam" => $gebruikersnaam, "idGebruiker" => $idGebruiker, "status" => $status);

    // Stuur de gebruiker naar de homepagina
    header("location: /index.php");
  } catch (Exception $e) {
    foutmelding(6, "/login.php", $e->getMessage()); // Geef een foutmelding weer
  } finally {
    sluitMysqli($connectie, $loginSelectStatement); // Probeer de connectie en statement te sluiten
  }
}

gebruikerInloggen(); // Probeer de gebruiker in te loggen
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen - Twitter</title>
  <link rel="stylesheet" href="/css/loginpage.css">
</head>

<body>
  <!-- Main: een gecentreerde div met daarin de content van de pagina -->
  <main>
    <!-- De header van het inlog-formulier -->
    <h1>Inloggen</h1>

    <!-- Het inlog-formulier, wordt terug gestuurd naar dezelfde pagina met POST data om te gebruiken voor het inlog-proces -->
    <form action="" method="POST">
      <!-- Het gebruikersnaam-veld: Komt in de POST data als "gebruikersnaam" en is een verplicht veld. -->
      <input type="text" placeholder="Gebruikersnaam" name="gebruikersnaam" required>
      <!-- Het wachtwoord-veld: Komt in de POST data als "wachtwoord" en is een verplicht veld. -->
      <input type="password" placeholder="Wachtwoord" name="wachtwoord" required>

      <!-- De knop om door te gaan naar het inlog-proces -->
      <input type="submit" value="Inloggen">
    </form>

    <!-- Een handy-dandy link naar de registreer-pagina -->
    <a href="/registreer.php">Geen account?</a>
  </main>
</body>

</html>