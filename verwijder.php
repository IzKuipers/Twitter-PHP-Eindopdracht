<?php
// Importeer de benodigde functies 
require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

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

  $statement = $connectie->prepare($query); // Bereid de vraag voor
  $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID

  if (!($statement->execute()))
    throw new Exception(); // Voer de vraag uit

  $statement->bind_result($postAuteurId); // Schrijf het resultaat naar $postAuteurId
  $statement->fetch(); // Voer fetch uit om $postAuteurId te schrijven. Dit hoeft maar één keer omdat ID's uniek zijn
  $statement->close(); // Sluit de auteur statement

  // De huidige gebruiker is niet de auteur van de post, de post niet worden verwijderd, stop.
  if ($gebruikerId !== $postAuteurId) {
    return;
  }

  // De tweede vraag aan de database: Verwijder alle posts wiens ID gelijk staat aan ?
  $query = "DELETE FROM posts WHERE idPost = ?";

  $statement = $connectie->prepare($query); // Bereid de vraag voor
  $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID

  if (!($statement->execute()))
    throw new Exception(); // Voer de vraag uit

  $statement->close(); // Sluit de verwijder statement
} catch (Exception $e) { // Anders...
  error_log($e->getMessage());
  foutmelding(Foutmeldingen::PostLikeMislukt, "/"); // Het is niet gelukt om de post te verwijderen, geef een foutmelding
} finally { // Ten slotte...
  sluitMysqli($connectie); // Sluit de connectie
  $_SESSION["toast"] = 7;
  header("location:/index.php");
}