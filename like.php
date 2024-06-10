<?php

// Importeer de benodigde functies voor deze pagina
require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

session_start(); // Start de sessie
verifieerIngelogd(); // Controleer of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een eventuele foutmelding weer

// Probeer een connectie te maken met de database
$connectie = verbindMysqli();

if (!$connectie) { // Connectie mislukt, geef een foutmelding en stop.
  foutmelding(7, "/", $e->getMessage());

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
  global $verwijderStatement, $updateStatement; // Maak de update statement globaal om ze in de finally te kunnen sluiten

  // De vraag aan de database: Geef mij de likes van alle posts wiens ID gelijk staat aan ?
  $query = "SELECT likes FROM posts WHERE idPost = ?";

  $verwijderStatement = $connectie->prepare($query); // Bereid de vraag voor
  $verwijderStatement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
  $verwijderStatement->execute(); // Voer de vraag uit
  $verwijderStatement->bind_result($likes); // Schrijf de likes naar de $likes variabele
  $verwijderStatement->fetch(); // Vraag het resultaat eenmalig op: ID's zijn uniek dus een while loop is overbodig 
  $verwijderStatement->close(); // Sluit de eerste statement

  $likes++; // Verhoog het aantal likes met 1

  // De tweede vraag aan de database: Verander de likes van alle posts wiens ID gelijk staat aan ?
  $query = "UPDATE posts SET likes = ? WHERE idPost = ?";

  $updateStatement = $connectie->prepare($query); // Bereid de tweede vraag voor
  $updateStatement->bind_param("ii", $likes, $id); // Vervang het vraagteken met de daadwerkelijke ID
  $updateStatement->execute(); // Voer de tweede vraag uit
} catch (Exception $e) { // Anders...
  foutmelding(7, "/", $e->getMessage());
} finally { // Ten slotte...
  sluitMysqli($connectie, $updateStatement); // Probeer de connectie en tweede statement te sluiten
  echo "<script>history.back();</script>"; // Echo een Javascript uitvoering om terug te gaan naar de vorige pagina
}