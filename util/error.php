<?php
require_once ("connectie.php");

session_start();

function foutmelding(int $id, string $continue = "", string $message = "")
{
  $aanvraagUri = $_SERVER["REQUEST_URI"];
  $huidigeUrl = parse_url($aanvraagUri);
  $paginaPad = $huidigeUrl['path'];

  $volgendePagina = $continue or $paginaPad;

  $_SESSION["error_message"] = $message;

  header("location: $paginaPad?error=$id&continue=$volgendePagina");
}

function geef_foutmelding_weer()
{
  if (!isset($_GET["error"], $_GET["continue"])) {
    unset($_SESSION["error_message"]);
    return;
  }

  $id = $_GET["error"];
  $continue = $_GET["continue"];

  $connectie = verbind_mysqli();

  $titel = "";
  $foutmelding = "";
  $details = isset($_SESSION["error_message"]) ? $_SESSION["error_message"] : "<geen>";

  try {
    global $statement, $titel, $foutmelding;

    $statement = $connectie->prepare("SELECT * FROM errors WHERE id = ?");
    $statement->bind_param("i", $id);
    $statement->execute();

    $statement->bind_result($id, $titel, $foutmelding);
    $statement->fetch();

    if (!$titel) {
      $titel = "Onbekende fout";
      $foutmelding = "Er is een fout opgetreden die niet bekend is. De details geven mogelijk meer informatie over de fout. Onze excuses voor het ongemak.";
    }

  } catch (Exception $e) {
    $titel = "Fout!";
    $foutmelding = "Er is een fout met ID $id is opgetreden, maar het is niet gelukt om met de server te verbinden om de details van de foutmelding op te vragen. Onze excuses voor het ongemak.";
  } finally {
    sluit_mysqli($connectie, $statement);
  }

  echo <<<HTML
    <div class="error-wrapper">
      <div class="error">
        <div class="content">
          <h1>$titel</h1>
          <p>$foutmelding</p>
          <p class="details">Details: <span>$details</span></p>
        </div>
        <div class="bottom">
          <a href="$continue">Sluiten</a>
        </div>        
      </div>
    </div>
  HTML;
}
