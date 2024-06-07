<?php

require ("./util/error.php");
require ("./util/session.php");

session_start();
geef_foutmelding_weer();

function gebruikerInloggen()
{
  if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST["gebruikersnaam"], $_POST["wachtwoord"])) {
    return;
  }

  $gebruikersnaam = $_POST["gebruikersnaam"];
  $wachtwoord = $_POST["wachtwoord"];

  $connectie = verbind_mysqli();

  $query = "SELECT idGebruiker,wachtwoord,status FROM gebruikers WHERE naam=?";

  try {
    global $idGebruiker, $passwordHash, $status, $statement;

    $statement = $connectie->prepare($query);
    $statement->bind_param("s", $gebruikersnaam);
    $statement->execute();
    $statement->bind_result($idGebruiker, $wachtwoordHash, $status);
    $statement->fetch();

  } catch (Exception $e) {
    sluit_mysqli($connectie, $statement);
    foutmelding(6, "", $e->getMessage());
  } finally {
    sluit_mysqli($connectie, $statement);
  }

  $wachtwoordKlopt = password_verify($wachtwoord, $wachtwoordHash);

  if (!$wachtwoordKlopt) {
    foutmelding(2, "/login.php");

    return;
  }

  $_SESSION["gebruiker"] = array("naam" => $gebruikersnaam, "idGebruiker" => $idGebruiker, "status" => $status);

  header("location: /index.php");

  var_dump($wachtwoordHash);
}

gebruikerInloggen();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form action="" method="POST">
    <input type="text" placeholder="Gebruikersnaam" name="gebruikersnaam">
    <input type="password" placeholder="Wachtwoord" name="wachtwoord">
    <input type="submit" value="Inloggen">
  </form>
  <a href="/registreer.php">Geen account?</a>
</body>

</html>