<?php

// Importeer de benodigde functies voor deze pagina
require_once ("./util/session.php");
require_once ("./util/posts.php");
require_once ("./util/connectie.php");

session_start(); // Start de session
verifieerIngelogd(); // Check of de gebruiker is ingelogd
$gebruiker = gebruikerUitSessie(); // De gebruiker vanuit de sessie

// Check of de nieuwe status in de POST data staat
if (!isset($_POST["status"])) {
  header("location:/"); // Geen nieuwe status, ga terug naar de homepagina
  die;
}

$status = $_POST["status"]; // De nieuwe status

// Maak verbinding met de database
$connectie = verbindMysqli();

try { // Probeer...
  // Vraag aan de database: Verander de status naar ? van alle gebruikers wiens ID gelijk staat aan ?
  $query = "UPDATE gebruikers SET status = ? WHERE idGebruiker = ?";

  $statement = $connectie->prepare($query); // Bereid de vraag voor
  $statement->bind_param("si", $status, $gebruiker["id"]); // Vervang de vraagtekens met de respectieve waarden
  $statement->execute(); // Voer de vraag uit
} catch (Exception $e) { // Anders...
  foutmelding(Foutmeldingen::StatusUpdateMislukt, "/", $e->getMessage()); // Geef een foutmelding als de status niet kon worden geüpdatet
} finally { // Ten slotte...
  sluitMysqli($connectie, $statement); // Probeer de connectie en statement te sluiten
  echo "<script>history.back();</script>"; // Stuur met Javascript de gebruiker terug naar de vorige pagina
}
