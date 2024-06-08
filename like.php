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
  global $idStatement, $updateStatement;

  $idStatement = $connectie->prepare("SELECT likes FROM posts WHERE idPost = ?");
  $idStatement->bind_param("i", $id);
  $idStatement->execute();
  $idStatement->bind_result($likes);
  $idStatement->fetch();
  $idStatement->close();

  $likes++;

  $query = "UPDATE posts SET likes = ? WHERE idPost = ?";

  $updateStatement = $connectie->prepare($query);
  $updateStatement->bind_param("ii", $likes, $id);
  $updateStatement->execute();
} catch (Exception $e) {
  foutmelding(7, "/", $e->getMessage());
} finally {
  sluit_mysqli($connectie, $updateStatement);
  echo "<script>history.back();</script>";
}