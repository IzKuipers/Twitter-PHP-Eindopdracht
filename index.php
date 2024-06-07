<?php
require_once ("./util/session.php");
require_once ("./util/error.php");
require_once ("./util/posts.php");

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
  <link rel="stylesheet" href="css/main.css">
  <title>Home - Twitter</title>
</head>

<body>
  <header>
    <div class="left">
      <h1>Twitter<span class="sub">(PHP)</span></h1>
    </div>
    <div class="right">
      <div class="user">
        <p class="username"><?= $gebruiker["naam"] ?></p>
        <a href="/uitloggen.php" class="material-icons-round">logout</a>
      </div>
    </div>
  </header>
  <main>
    <form action="/stuurpost.php" method="POST" class="post-form">
      <textarea class="" placeholder="Wat gebeurt er?!" name="bericht" required></textarea>
      <input type="submit" value="Post">
    </form>
    <?php post_lijst() ?>
  </main>
  <footer>Je hebt het einde bereikt!</footer>
</body>

</html>