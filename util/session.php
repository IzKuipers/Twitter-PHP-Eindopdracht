<?php

function verifieer_ingelogd()
{
  session_start();

  if (!isset($_SESSION["sessie_id"])) {
    header("Location: /login.php");
  }

  $sessie = $_SESSION["sessie_id"];

  $conn = new mysqli("127.0.0.1", "root", "", "eindopdracht");


}