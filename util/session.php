<?php

function verifieer_ingelogd()
{
  session_start();

  if (!isset($_SESSION["gebruikersid"])) {
    header("Location: ");
  }
}