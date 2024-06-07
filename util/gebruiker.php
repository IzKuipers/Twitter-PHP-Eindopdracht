<?php

function gebruiker_ophalen($id)
{
  $connectie = verbind_mysqli();

  $query = "SELECT idGebruiker,naam,status FROM gebruikers WHERE idGebruiker = ?";

  try {
    global $statement;

    $statement = $connectie->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->fetch();
    $statement->bind_result($idGebruiker, $naam, $status);

    return array("idGebruiker" => $idGebruiker, "naam" => $naam, "status" => $status);
  } catch (Exception $e) {
    return array("idGebruiker" => -1, "naam" => "", "status" => "");
  } finally {
    sluit_mysqli($connectie, $statement);
  }
}