<?php

// Deze functie is een middleman om gemakkelijk met een ID een gebruiker op te halen.
function gebruikerOphalen($id)
{
  // Maak verbinding met de database dmv verbindMysqli()
  $connectie = verbindMysqli();

  try { // Probeer...
    global $selectStatement; // Maak de statement global voor het sluiten.

    // De vraag aan de database: Geef mij de ID, naam en status van alle gebruikers wiens ID gelijk staat aan ?
    $query = "SELECT idGebruiker,naam,status FROM gebruikers WHERE idGebruiker = ?";

    $selectStatement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $selectStatement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
    $selectStatement->execute(); // Voer de vraag uit
    $selectStatement->bind_result($idGebruiker, $naam, $status); // Schrijf het resultaat naar de respectieve variabelen. De volgorde is hetzelfde als in de tabel.
    $selectStatement->fetch(); // We hoeven fetch() maar eenmalig op te roepen omdat ID's toch uniek zijn, een while loop is hier overbodig.

    // Geef een associative array terug met de eigenschappen van de gebruiker
    return array("idGebruiker" => $idGebruiker, "naam" => $naam, "status" => $status);
  } catch (Exception $e) { // Anders...
    // Geef een associative array terug met "dummy informatie"
    return array("idGebruiker" => -1, "naam" => "", "status" => "");
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten
    sluitMysqli($connectie, $selectStatement);
  }
}