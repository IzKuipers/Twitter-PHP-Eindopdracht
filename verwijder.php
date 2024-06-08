<?php

require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

session_start();
verifieer_ingelogd();
geef_foutmelding_weer();

$gebruiker = gebruiker_uit_sessie();
$gebruikerId = $gebruiker["id"];

$connectie = verbind_mysqli();

if (!isset($_GET["id"])) {
  header("location:/");

  die;
}

$id = $_GET["id"];

try {
  global $id_statement;

  $auteur_statement = $connectie->prepare("SELECT auteur FROM posts WHERE idPost = ?");
  $auteur_statement->bind_param("i", $id);
  $auteur_statement->execute();
  $auteur_statement->bind_result($postAuteurId);
  $auteur_statement->fetch();
  $auteur_statement->close();

  if ($gebruikerId !== $postAuteurId) {
    return;
  }

  $id_statement = $connectie->prepare("DELETE FROM posts WHERE idPost = ?");
  $id_statement->bind_param("i", $id);
  $id_statement->execute();
  $id_statement->close();
} catch (Exception $e) {
  foutmelding(7, "/", $e->getMessage());
} finally {
  sluit_mysqli($connectie);
  echo "<script>history.back();</script>";
}