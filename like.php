<?php

// Importeer de benodigde functies voor deze pagina
require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

verifieerIngelogd(); // Controleer of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een eventuele foutmelding weer

// Probeer een connectie te maken met de database
$connectie = verbindMysqli();

if (!$connectie) { // Connectie mislukt, geef een foutmelding en stop.
  foutmelding(Foutmeldingen::VerbindingMislukt, "/", $e->getMessage());

  die;
}

// Controleer of er een post-ID is in de GET parameters
if (!isset($_GET["id"])) {
  // Geen post-ID: ga terug naar de homepagina
  header("location:/");

  die;
}

$id = $_GET["id"]; // De ID van de post

try { // Probeer...
  // De vraag aan de database: Geef mij de likes van alle posts wiens ID gelijk staat aan ?
  $query = "SELECT likes FROM posts WHERE idPost = ?";

  $statement = $connectie->prepare($query); // Bereid de vraag voor
  $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
  $statement->execute(); // Voer de vraag uit
  $statement->bind_result($likes); // Schrijf de likes naar de $likes variabele
  $statement->fetch(); // Vraag het resultaat eenmalig op: ID's zijn uniek dus een while loop is overbodig 
  $statement->close(); // Sluit de eerste statement

  $likes++; // Verhoog het aantal likes met 1

  // De tweede vraag aan de database: Verander de likes van alle posts wiens ID gelijk staat aan ?
  $query = "UPDATE posts SET likes = ? WHERE idPost = ?";

  $statement = $connectie->prepare($query); // Bereid de tweede vraag voor
  $statement->bind_param("ii", $likes, $id); // Vervang het vraagteken met de daadwerkelijke ID
  $statement->execute(); // Voer de tweede vraag uit
} catch (Exception $e) { // Anders...
  foutmelding(Foutmeldingen::PostLikeMislukt, "/", $e->getMessage());
} finally { // Ten slotte...
  sluitMysqli($connectie, $statement); // Probeer de connectie en tweede statement te sluiten
  $_SESSION["toast"] = 4;
  echo "<script>history.back();</script>";
}