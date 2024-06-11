<?php
require_once ("./util/session.php");
require_once ("./util/error.php");
require_once ("./util/posts.php");
require_once ("./ui/headerbar.php");

verifieerIngelogd(); // Check of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een potentiele foutmelding weer

$gebruiker = gebruikerUitSessie(); // Haal de gebruiker uit de session (voor de header van de pagina)
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/homepage.css">
  <link rel="shortcut icon" href="/images/logo.png" type="image/png">
  <link rel="manifest" href="/manifest.webmanifest">
  <title>Home - Twitter</title>
</head>

<body>
  <!-- De headerbar -->
  <?php HeaderBar($gebruiker) ?>
  <!-- De daadwerkelijke content van de pagina -->
  <main>
    <!-- Het formulier voor het versturen van een post. Hij gebruikt de POST methode en
         stuurt door naar /stuurpost.php, waar de logica voor het sturen van de post leeft. -->
    <form action="/stuurpost.php" method="POST" class="post-form">
      <!-- De textarea waar de gebruiker hun bericht in kan typen -->
      <textarea class="" placeholder="Wat gebeurt er?!" name="bericht" required maxlength="512" rows="3"></textarea>
      <!-- De knop om de post te versturen -->
      <input type="submit" value="Post">
    </form>
    <!-- De functie die de tweets weergeeft-->
    <?php postLijst(postsOphalen()) ?>
  </main>
  <!-- De footer duidt enkel het einde van de posts aan-->
  <footer>Je hebt het einde bereikt!</footer>
</body>

</html>