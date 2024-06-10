<?php
// Importeer de benodigde functies 
require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

session_start(); // Start de sessie
verifieerIngelogd(); // Check of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een eventuele foutmelding weer 

$gebruiker = gebruikerUitSessie(); // Haal de huidige gebruiker op uit de sessie
$gebruikerId = $gebruiker["id"]; // De ID van de gebruiker

// Maak verbinding met de database
$connectie = verbindMysqli();

// Controleer of de ID van de post om te verwijderen in de GET data staat
if (!isset($_GET["id"])) {
  header("location:/"); // Geen ID, ga terug naar de index pagina

  die;
}

$id = $_GET["id"]; // De ID van de post

try { // Probeer...
  // De vraag aan de database: Geef mij de auteur van alle posts wiens ID gelijk staat aan ?
  $query = "SELECT auteur FROM posts WHERE idPost = ?";

  $auteurStatement = $connectie->prepare($query); // Bereid de vraag voor
  $auteurStatement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
  $auteurStatement->execute(); // Voer de vraag uit
  $auteurStatement->bind_result($postAuteurId); // Schrijf het resultaat naar $postAuteurId
  $auteurStatement->fetch(); // Voer fetch uit om $postAuteurId te schrijven. Dit hoeft maar één keer omdat ID's uniek zijn
  $auteurStatement->close(); // Sluit de auteur statement

  // De huidige gebruiker is niet de auteur van de post, de post niet worden verwijderd, stop.
  if ($gebruikerId !== $postAuteurId) {
    return;
  }

  // De tweede vraag aan de database: Verwijder alle posts wiens ID gelijk staat aan ?
  $query = "DELETE FROM posts WHERE idPost = ?";

  $verwijderStatement = $connectie->prepare($query); // Bereid de vraag voor
  $verwijderStatement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
  $verwijderStatement->execute(); // Voer de vraag uit
  $verwijderStatement->close(); // Sluit de verwijder statement
} catch (Exception $e) { // Anders...
  foutmelding(7, "/", $e->getMessage()); // Het is niet gelukt om de post te verwijderen, geef een foutmelding
} finally { // Ten slotte...
  sluitMysqli($connectie); // Sluit de connectie
  echo "<script>history.back();</script>"; // Gebruik Javascript om de gebruiker terug te sturen naar de index pagina
}