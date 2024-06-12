<?php

// Deze functie is een middleman om gemakkelijk met een ID een gebruiker op te halen.
function gebruikerOphalen($id)
{
  // Maak verbinding met de database dmv verbindMysqli()
  $connectie = verbindMysqli();

  try { // Probeer...
    // De vraag aan de database: Geef mij de ID, naam en status van alle gebruikers wiens ID gelijk staat aan ?
    $query = "SELECT idGebruiker,naam,status FROM gebruikers WHERE idGebruiker = ?";

    $statement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $statement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID

    if (!($statement->execute()))
      throw new Exception(); // Voer de vraag uit

    $statement->bind_result($idGebruiker, $naam, $status); // Schrijf het resultaat naar de respectieve variabelen. De volgorde is hetzelfde als in de tabel.
    $statement->fetch(); // We hoeven fetch() maar eenmalig op te roepen omdat ID's toch uniek zijn, een while loop is hier overbodig.

    if (!$idGebruiker)
      throw new Exception();

    // Geef een associative array terug met de eigenschappen van de gebruiker
    return array("idGebruiker" => $idGebruiker, "naam" => $naam, "status" => $status);
  } catch (Exception $e) { // Anders...
    // Geef een associative array terug met "dummy informatie"
    return array("idGebruiker" => -1, "naam" => "Onbekende gebruiker", "status" => "");
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten
    sluitMysqli($connectie, $statement);
  }
}