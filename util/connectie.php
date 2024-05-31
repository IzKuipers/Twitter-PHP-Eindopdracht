<?php
require ("error.php");

function verbind_mysqli()
{
  $host = "127.0.1";
  $user = "root";
  $pass = "";
  $database = "eindopdracht";

  $connection = new mysqli($host, $user, $pass, $database);

  if ($connection->connect_error) {
    foutmelding(3);
  }
}