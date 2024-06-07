<?php

require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

session_start();
verifieer_ingelogd();
geef_foutmelding_weer();

$connectie = verbind_mysqli();

if (!isset($_GET["id"])) {
  header("location:/");

  die;
}

$id = $_GET["id"];

try {
  global $id_statement;

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