<?php
require ("./util/error.php");

geef_foutmelding_weer();

function registreerGebruiker()
{
  if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST["gebruikersnaam"]) || !isset($_POST["wachtwoord"]) || !isset($_POST["wachtwoordOpnieuw"])) {
    return;
  }

  $gebruikersnaam = $_POST["gebruikersnaam"];
  $wachtwoord = $_POST["wachtwoord"];
  $wachtwoordOpnieuw = $_POST["wachtwoordOpnieuw"];

  if ($wachtwoord != $wachtwoordOpnieuw) {
    foutmelding(4);

    return;
  }

  $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

  $connectie = verbind_mysqli();

  try {
    global $statement;

    $query = "INSERT INTO gebruikers(naam,wachtwoord) values (?,?)";
    $statement = $connectie->prepare($query);
    $statement->bind_param("ss", $gebruikersnaam, $hash);

    $statement->execute();
  } catch (Exception $e) {
    foutmelding(5, "", $e->getMessage());

    return;
  } finally {
    sluit_mysqli($connectie, $statement);
  }

  header("location: /login.php");
}

registreerGebruiker();
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