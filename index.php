<?php
require_once ("./util/session.php");
require_once ("./util/error.php");
require_once ("./util/posts.php");

session_start(); // Start de session
verifieerIngelogd(); // Check of de gebruiker is ingelogd
geefFoutmeldingWeer(); // Geef een potentiele foutmelding weer

$gebruiker = gebruikerUitSessie(); // Haal de gebruiker uit de session (voor de header van de pagina)
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/homepage.css">
  <title>Home - Twitter</title>
</head>

<body>
  <!-- De headerbar -->
  <header>
    <!-- Het Twitter merk met een vleugje PHP -->
    <div class="left">
      <h1>Twitter<span class="sub">(PHP)</span></h1>
    </div>
    <!-- Aan de rechter kant: De gebruikersnaam met een knop om uit te loggen-->
    <div class="right">
      <div class="user">
        <!-- Het ?= teken is een kortere versie van ? echo -->
        <p class="username"><?= $gebruiker["naam"] ?></p>
        <!-- We gebruiken /uitloggen.php voor het uitloggen-proces -->
        <a href="/uitloggen.php" class="material-icons-round">logout</a>
      </div>
    </div>
  </header>
  <!-- De daadwerkelijke content van de pagina -->
  <main>
    <!-- Het formulier voor het versturen van een post. Hij gebruikt de POST methode en
         stuurt door naar /stuurpost.php, waar de logica voor het sturen van de post leeft. -->
    <form action="/stuurpost.php" method="POST" class="post-form">
      <!-- De textarea waar de gebruiker hun bericht in kan typen -->
      <textarea class="" placeholder="Wat gebeurt er?!" name="bericht" required></textarea>
      <!-- De knop om de post te versturen -->
      <input type="submit" value="Post">
    </form>
    <!-- De functie die de tweets weergeeft-->
    <?php postLijst() ?>
  </main>
  <!-- De footer duidt enkel het einde van de posts aan-->
  <footer>Je hebt het einde bereikt!</footer>
</body>

</html>