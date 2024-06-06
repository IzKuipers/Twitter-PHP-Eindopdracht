<?php

function verifieer_ingelogd()
{
  if (!isset($_SESSION["gebruikerid"])) {
    header("Location: /login.php");
  }

  return;
}