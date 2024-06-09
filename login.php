<?php

require_once ("./util/error.php");
require_once ("./util/session.php");

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

  if (!$connectie) {
    foutmelding(3, "/login.php");
    return;
  }

  $query = "SELECT idGebruiker,wachtwoord,status FROM gebruikers WHERE naam=?";

  try {
    global $idGebruiker, $passwordHash, $status, $statement;

    $statement = $connectie->prepare($query);
    $statement->bind_param("s", $gebruikersnaam);
    $statement->execute();
    $statement->bind_result($idGebruiker, $wachtwoordHash, $status);
    $statement->fetch();

    if (!$idGebruiker) {
      foutmelding(1, "/login.php", "idGebruiker was NULL");

      return;
    }

    $wachtwoordKlopt = password_verify($wachtwoord, $wachtwoordHash);

    if (!$wachtwoordKlopt) {
      foutmelding(2, "/login.php", "Wachtwoord fout");

      return;
    }

    $_SESSION["gebruiker"] = array("naam" => $gebruikersnaam, "idGebruiker" => $idGebruiker, "status" => $status);

    // header("location: /index.php");
  } catch (Exception $e) {
    sluit_mysqli($connectie, $statement);
    foutmelding(6, "/login.php", $e->getMessage());
  } finally {
    sluit_mysqli($connectie, $statement);
  }

}

gebruikerInloggen();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen - Twitter</title>
  <link rel="stylesheet" href="/css/loginpage.css">
</head>

<body>
  <main>
    <h1>Inloggen</h1>
    <form action="" method="POST">
      <input type="text" placeholder="Gebruikersnaam" name="gebruikersnaam" required>
      <input type="password" placeholder="Wachtwoord" name="wachtwoord" required>
      <input type="submit" value="Inloggen">
    </form>
    <a href="/registreer.php">Geen account?</a>
  </main>
</body>

</html>