<?php

require_once ("./util/error.php");
require_once ("./util/session.php");

weZijnMisschienOffline(); // Controleer of de database online is
geefFoutmeldingWeer(); // Geef de eventuele foutmelding weer

if (isset($_SESSION["gebruiker"])) {
  header("location:/");
}

// Deze functie wordt gebruikt om de gebruiker in te loggen via de POST data
function gebruikerInloggen()
{
  // Check via de request method en POST data of de gebruiker probeert in te loggen
  if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST["gebruikersnaam"], $_POST["wachtwoord"])) {
    return; // Gebruiker probeert niet in te loggen, stop.
  }

  $gebruikersnaam = $_POST["gebruikersnaam"]; // De gebruikersnaam die de gebruiker heeft ingevoerd
  $gebruikersnaamVeilig = htmlspecialchars($gebruikersnaam);
  $wachtwoord = $_POST["wachtwoord"]; // Het wachtwoord die de gebruiker heeft ingevoerd

  // Maak verbinding met de database
  $connectie = verbindMysqli();

  if (!$connectie) {
    weZijnOffline(); // Connectie mislukt, naar de offline pagina dan maar!
    return;
  }


  try { // Probeer...
    // De vraag aan de database: Geef mij de ID, de wachtwoord-hash en de status van alle gebruikers wiens naam gelijk staat aan ?
    $query = "SELECT idGebruiker,wachtwoord,status FROM gebruikers WHERE naam=?";

    $statement = $connectie->prepare($query); // Bereid de vraag voor
    $statement->bind_param("s", $gebruikersnaamVeilig); // Vervang het vraagteken met de daadwerkelijke gebruikersnaam

    if (!($statement->execute()))
      throw new Exception(); // Voer de vraag uit

    $statement->bind_result($idGebruiker, $wachtwoordHash, $status); // Schrijf het resultaat naar de respectieve variabelen
    $statement->fetch(); // Vraag het resultaat op. Dit hoeft maar eenmalig te gebeuren omdat ID's uniek zijn

    // Controleer of de gebruiker bestaat
    if (!$idGebruiker) {
      foutmelding(Foutmeldingen::GebruikerNietGevonden, "/login.php", "Gebruiker $gebruikersnaam niet gevonden"); // Gebruiker bestaat niet, geef een foutmelding weer

      return;
    }

    // Controleer of het wachtwoord klopt met de builtin hashing-functie password_verify()
    $wachtwoordKlopt = password_verify($wachtwoord, $wachtwoordHash);

    if (!$wachtwoordKlopt) {
      foutmelding(Foutmeldingen::WachtwoordOnjuist, "/login.php", "Wachtwoord fout"); // Het wachtwoord klopt niet, geef een foutmelding weer

      return;
    }

    // Schrijf de eigenschappen van de gebruiker naar de session om later te gebruiken
    $_SESSION["gebruiker"] = array("naam" => $gebruikersnaam, "idGebruiker" => $idGebruiker, "status" => $status);

    // Stuur de gebruiker naar de homepagina
    $_SESSION["toast"] = 2;
    header("location: /");
  } catch (Exception $e) {
    error_log($e->getMessage());
    foutmelding(Foutmeldingen::ControleMislukt, "/login.php"); // Geef een foutmelding weer
  } finally {
    sluitMysqli($connectie, $statement); // Probeer de connectie en statement te sluiten
  }
}

gebruikerInloggen(); // Probeer de gebruiker in te loggen
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen - Twitter</title>
  <link rel="stylesheet" href="/css/loginpage.css">
  <link rel="shortcut icon" href="/images/logo.png" type="image/png">
  <link rel="manifest" href="/manifest.webmanifest">
</head>

<body>
  <!-- Main: een gecentreerde div met daarin de content van de pagina -->
  <main>
    <img src="/images/logo.png" alt="">
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