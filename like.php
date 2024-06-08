<?php

require_once ("./util/connectie.php");
require_once ("./util/error.php");
require_once ("./util/session.php");

session_start();
verifieer_ingelogd();
geef_foutmelding_weer();

$connectie = verbind_mysqli();

if (!$connectie) {
  foutmelding(7, "/", $e->getMessage());

  die;
}

if (!isset($_GET["id"])) {
  header("location:/");

  die;
}

$id = $_GET["id"];

try {
  global $id_statement, $update_statement;

  $id_statement = $connectie->prepare("SELECT likes FROM posts WHERE idPost = ?");
  $id_statement->bind_param("i", $id);
  $id_statement->execute();
  $id_statement->bind_result($likes);
  $id_statement->fetch();
  $id_statement->close();

  $likes++;

  $query = "UPDATE posts SET likes = ? WHERE idPost = ?";

  $update_statement = $connectie->prepare($query);
  $update_statement->bind_param("ii", $likes, $id);
  $update_statement->execute();
} catch (Exception $e) {
  foutmelding(7, "/", $e->getMessage());
} finally {
  sluit_mysqli($connectie, $update_statement);
  echo "<script>history.back();</script>";
}