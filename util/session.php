<?php

function verifieer_ingelogd()
{
  session_start();

  if (!isset($_SESSION["gebruiker"])) {
    header("Location: /login.php");
    die;
  }

  return;
}

function gebruiker_uit_sessie()
{
  if (!isset($_SESSION["gebruiker"])) {
    return array("naam" => "", "id" => -1, "status" => "");
  }

  $gebruikersnaam = $_SESSION["gebruiker"]["naam"];
  $id = $_SESSION["gebruiker"]["idGebruiker"];
  $status = $_SESSION["gebruiker"]["status"];

  return array("naam" => $gebruikersnaam, "id" => $id, "status" => $status);
}

function uitloggen()
{
  unset($_SESSION["gebruiker"]);
  header("location:/login.php");
}