<?php
require ("./util/session.php");
require ("./util/error.php");
require ("./util/posts.php");

session_start();
verifieer_ingelogd();
geef_foutmelding_weer();

$gebruiker = gebruiker_uit_sessie();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Twitter</title>
</head>

<body>
  <header>
    <div class="left">
      <h1><span class="sub">(de echte)</span> Twitter</h1>
    </div>
    <div class="right">
      <div class="user">
        <p class="username"><?= $gebruiker["naam"] ?></p>
        <a href="/uitloggen.php">Uitloggen</a>
      </div>
    </div>
  </header>
  <main>
    <form action="/stuurpost.php" method="POST" class="post-form">
      <textarea class="" placeholder="Wat gebeurt er?!" name="bericht"></textarea>
      <input type="submit" value="Post">
    </form>
    <?php post_lijst() ?>
  </main>
</body>

</html>