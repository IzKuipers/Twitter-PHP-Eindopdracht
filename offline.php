<?php
require_once ("./util/connectie.php");

// Probeer met de database te verbinden
$connectie = verbindMysqli(false);

// Controleer of we daadwerkelijk offline zijn
if ($connectie) {
  sluitMysqli($connectie);
  header("location:/"); // We zijn online: ga terug naar de home pagina
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/offline.css">
  <link rel="shortcut icon" href="/images/logo.png" type="image/png">
  <link rel="manifest" href="/manifest.webmanifest">
  <title>We zijn offline!</title>
</head>

<body>
  <!-- De main: een gecentreerde div met daarin de content van de pagina-->
  <main>
    <!-- Een icoontje om het probleem te benadrukken -->
    <span class="material-icons-round">heart_broken</span>

    <!-- Een title en bericht om te laten weten wat er aan de hand is -->
    <h1>We zijn offline!</h1>
    <p>Sorry! We zijn momenteel niet bereikbaar. We proberen zo snel als mogelijk weer online te komen, probeer het
      later opnieuw.</p>

    <!-- Een link die terug gaat naar dezelfde pagina om de offline-check opnieuw uit te voeren -->
    <a href="">Opnieuw</a>
  </main>
</body>

</html>