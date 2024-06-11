<?php

require_once ("./util/session.php");
require_once ("./util/error.php");

session_start(); // Start de sessie
verifieerIngelogd(); // Check of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een eventuele foutmelding weer

// Controleer of er een daadwerkelijke post in de POST data staat (inception?)
if (!isset($_POST["bericht"])) {
  header("location:/"); // Geen post, stuur de gebruiker terug naar de index pagina
}

$gebruiker = gebruikerUitSessie(); // Haal de gebruiker op uit de sessie

$bericht = $_POST["bericht"]; // Het bericht van de gebruiker
$reageertOp = isset($_POST["reactieOp"]) ? $_POST["reactieOp"] : NULL;
$likes = 0; // De likes van de post (standaard 0)

// Maak verbinding met de database
$connectie = verbindMysqli();

try { // Probeer...
  // De vraag aan de database: Maak een rij aan in de posts tabel met body ?, likes ?, auteur-id ? en reageert-op ?
  $query = "INSERT INTO posts(body,likes,auteur,repliesTo) VALUES (?,?,?,?)";

  $statement = $connectie->prepare($query); // Bereid de vraag voor
  $statement->bind_param("siii", $bericht, $likes, $gebruiker["id"], $reageertOp); // Vervang de variabelen met hun specifieke waarden
  $statement->execute(); // Voer de vraag uit
} catch (Exception $e) { // Anders...
  foutmelding(Foutmeldingen::VersturenMislukt, "/index.php", $e->getMessage()); // Posten mislukt: geef een foutmelding weer
} finally { // Ten slotte...
  // Probeer de connectie en statement te sluiten
  sluitMysqli($connectie, $statement);
}

$succesCode = !$reageertOp ? 3 : 5;

$_SESSION["succes"] = $succesCode;

// Stuur de gebruiker terug naar de index pagina
header("location:/");
