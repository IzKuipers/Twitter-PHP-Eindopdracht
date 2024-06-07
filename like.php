<?php

require ("./util/connectie.php");

$connectie = verbind_mysqli();

$id = $_POST["id"];



$query = "UPDATE ";
$statement = $connectie->prepare();