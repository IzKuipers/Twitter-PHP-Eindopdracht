<?php
require ("./util/error.php");

session_start();
geef_foutmelding_weer();

function loginGebruiker()
{
  if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST["gebruikersnaam"], $_POST["wachtwoord"])) {
    return;
  }

  $gebruikersnaam = $_POST["gebruikersnaam"];
  $wachtwoord = $_POST["wachtwoord"];

  $conn = new mysqli("127.0.0.1", "root", "", "eindopdracht");

  if ($conn->connect_error) {
    return; // ERROR HANDLING
  }

  $query = "SELECT idGebruiker,wachtwoord FROM gebruikers WHERE naam=?";
  $statement = $conn->prepare($query);
  $statement->bind_param("s", $gebruikersnaam);
  $statement->execute();
  $statement->bind_result($idGebruiker, $wachtwoordHash);
  $statement->fetch();

  $wachtwoordKlopt = password_verify($wachtwoord, $wachtwoordHash);

  if (!$wachtwoordKlopt) {
    foutmelding(2, "/login.php");

    return;
  }

  $_SESSION["gebruikerid"] = $idGebruiker;

  header("location: /index.php");

  var_dump($wachtwoordHash);
}

loginGebruiker();
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