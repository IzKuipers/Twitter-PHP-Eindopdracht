<?php

require_once ("./util/session.php");
require_once ("./util/error.php");

session_start(); // Start de sessie
verifieer_ingelogd(); // Check of de gebruiker is ingelogd
geef_foutmelding_weer(); // Geef een eventuele foutmelding weer

// Controleer of er een daadwerkelijke post in de POST data staat (inception?)
if (!isset($_POST["bericht"])) {
  header("location:/"); // Geen post, stuur de gebruiker terug naar de index pagina
}

$gebruiker = gebruiker_uit_sessie(); // Haal de gebruiker op uit de sessie

$bericht = $_POST["bericht"]; // Het bericht van de gebruiker
$likes = 0; // De likes van de post (standaard 0)

// Maak verbinding met de database
$connectie = verbind_mysqli();

try { // Probeer...
  global $postInsertStatement; // Maak de statement globaal om deze later te kunnen sluiten

  // De vraag aan de database: Maak een rij aan in de posts tabel met body ?, likes ? en auteur-id ?
  $query = "INSERT INTO posts(body,likes,auteur) VALUES (?,?,?)";

  $postInsertStatement = $connectie->prepare($query); // Bereid de vraag voor
  $postInsertStatement->bind_param("sii", $bericht, $likes, $gebruiker["id"]); // Vervang de variabelen met hun specifieke waarden
  $postInsertStatement->execute(); // Voer de vraag uit
} catch (Exception $e) { // Anders...
  foutmelding(8, "/index.php", $e->getMessage()); // Posten mislukt: geef een foutmelding weer
} finally { // Ten slotte...
  // Probeer de connectie en statement te sluiten
  sluit_mysqli($connectie, $postInsertStatement);
}

// Stuur de gebruiker terug naar de index pagina
header("location:/");