<?php


function loginGebruiker()
{
  if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST["gebruikersnaam"]) || !isset($_POST["wachtwoord"]) || !isset($_POST["wachtwoordOpnieuw"])) {
    return;
  }

  $gebruikersnaam = $_POST["gebruikersnaam"];
  $wachtwoord = $_POST["wachtwoord"];
  $wachtwoordOpnieuw = $_POST["wachtwoordOpnieuw"];

  if ($wachtwoord != $wachtwoordOpnieuw) {
    header("location: /error.php?id=1&continue=%2Fregistreer.php");

    return;
  }

  $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

  $conn = new mysqli("127.0.0.1", "root", "", "eindopdracht");

  if ($conn->connect_error) {
    return; // ERROR HANDLING
  }

  $query = "INSERT INTO gebruikers(naam,wachtwoord) values (?,?)";
  $statement = $conn->prepare($query);
  $statement->bind_param("ss", $gebruikersnaam, $hash);

  $statement->execute();

  header("location: /login.php");
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
  <h1>Registreren</h1>
  <form action="" method="POST">
    <input type="text" name="gebruikersnaam" placeholder="Gebruikersnaam">
    <input type="password" name="wachtwoord" placeholder="Wachtwoord">
    <input type="password" name="wachtwoordOpnieuw" placeholder="Wachtwoord nogmaals">
    <input type="submit" value="Registreren">
  </form>
</body>

</html>