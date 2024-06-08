<?php

function verbind_mysqli($geef_foutmelding = true)
{
  $host = "127.0.0.1"; // Op Linux is het 127.0.0.1 in plaats van localhost!
  $user = "root";
  $pass = "";
  $database = "eindopdracht";
  try {
    $connectie = new mysqli($host, $user, $pass, $database);

    // Check connection
    if ($connectie->connect_error) {
      throw new Exception();
    }

    return $connectie;

    // We gaan er vanuit dat de verbind_mysqli() functie is vervolgd met een
    // sluit_mysqli() referentie om de connectie te sluiten.
  } catch (Exception $e) {
    if ($geef_foutmelding)
      foutmelding(3, "/");
    return;
  }
}

function sluit_mysqli($connectie, ...$statements)
{
  // Ik gebruik instanceof om te checken of de statement en
  // connectie uberhaupt bestaan voordat ik ze probeer te sluiten,
  // dat voorkomt onverwachte fouten.
  foreach ($statements as $statement) {
    if (isset($statement) && $statement instanceof mysqli_stmt) {
      try {
        $statement->close();
      } catch (Exception $e) {
        printf("Statement sluiten onderbroken: " . $e->getMessage());
      }
    }
  }

  if (isset($connectie) && $connectie instanceof mysqli) {
    $connectie->close();
  }
}