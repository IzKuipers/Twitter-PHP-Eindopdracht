<?php

function verbind_mysqli($geef_foutmelding = true)
{
  // Note to self: $pass: use pma/Izaak/passwords[1] (service "pma") for the password
  // to use the code in the context of Cortex deployment. 2FA code 3 applies here.
  //
  // - Izaak Kuipers, June 9th 2024 @ 1:14PM

  $host = "127.0.0.1"; // De hostname (of evt. de IP) van de SQL database (op Linux is het 127.0.0.1 in plaats van localhost)
  $user = "root"; // De gebruikersnaam van de database
  $pass = ""; // Het wachtwoord van de database gebruiker
  $database = "eindopdracht"; // De SQL database om mee te verbinden (zie importeer-mij.sql voor de database import)

  try {
    // Maak een nieuwe MySQLi class instantie aan om met de database te communiceren
    $connectie = new mysqli($host, $user, $pass, $database);

    // Check of de connectie is voltooid
    if ($connectie->connect_error) {
      throw new Exception(); // Connectie mislukt, geef foutmelding.
    }

    return $connectie;

    // We gaan er vanuit dat de verbind_mysqli() functie is vervolgd met een
    // sluit_mysqli() referentie om de connectie te sluiten.
  } catch (Exception $e) {
    // Dit is conditioneel: Er zijn instanties waar een visuele foutmelding niet van toepassing is
    // en op een andere manier wordt afgehandeld. Vandaar de $geef_foutmelding.
    if ($geef_foutmelding)
      foutmelding(3, "/"); // Geef foutmelding 3 weer aan de gebruiker
  }
}

function sluit_mysqli($connectie, ...$statements)
{
  // Opmerking: Ik gebruik instanceof om te checken of de statement en
  //            connectie uberhaupt instanties zijn van de classes voordat
  //            ik ze probeer te sluiten, dat voorkomt onverwachte fouten.

  // Voor iedere statement in de spreaded array $statements, doe...
  foreach ($statements as $statement) {
    // Controleer of iedere statement ook wel echt een statement is, en niet NULL bijvoorbeeld.
    if (isset($statement) && $statement instanceof mysqli_stmt) {
      try { // Probeer hem te sluiten
        $statement->close();
      } catch (Exception $e) {
        // Gooi een foutmelding in de console. Dit is voor de server manager/developer, dit krijgt de gebruiker nooit te zien.
        // Een foutmelding zal hier alleen voorkomen als de statement al ergens anders is gesloten.
        printf("Statement sluiten onderbroken: " . $e->getMessage());
      }
    }
  }

  // Controleer of de connectie ook echt een mysqli instantie is, en niet NULL bijvoorbeeld.
  if (isset($connectie) && $connectie instanceof mysqli) {
    try { // Probeer hem te sluiten
      $connectie->close();
    } catch (Exception $e) {
      // Gooi een foutmelding in de console. Dit is voor de server manager/developer, dit krijgt de gebruiker nooit te zien.
      // Een foutmelding zal hier alleen voorkomen als de connectie al ergens anders is gesloten.
      printf("Connectie sluiten onderbroken: " . $e->getMessage());
    }
  }
}