<?php

// Dit is een van de meest essentiele functies. Het controleert of de
// gebruiker is ingelogd, en stuurt de gebruiker terug naar de inlogpagina als dat niet zo is.
function verifieerIngelogd()
{
  session_start(); // Start de sessie

  // Check of de gebruiker in de sessie staat
  if (!isset($_SESSION["gebruiker"])) {
    $_SESSION["toast"] = 9;
    header("Location: /login.php"); // Gebruiker staat niet in de sessie, stuur door naar de login pagina
    die;
  }

  return; // Gebruiker is ingelogd, onderneem verder geen actie.
}

// Deze functie haalt de gebruiker uit de sessie
function gebruikerUitSessie()
{
  // Check of de gebruiker in de sessie staat
  if (!isset($_SESSION["gebruiker"])) {
    // Gebruiker staat niet in de sessie, stuur "dummy informatie" terug
    return array("naam" => "", "id" => -1, "status" => "");
  }

  $gebruikersnaam = $_SESSION["gebruiker"]["naam"]; // De gebruikersnaam
  $id = $_SESSION["gebruiker"]["idGebruiker"]; // De ID van de gebruiker
  $status = $_SESSION["gebruiker"]["status"]; // De status van de gebruiker (TODO!)

  // Stuur de eigenschappen van de gebruiker terug
  return array("naam" => $gebruikersnaam, "id" => $id, "status" => $status);
}

// Deze functie is gebruikt om de gebruiker uit te loggen.
function uitloggen()
{
  session_start(); // Start de sessie

  unset($_SESSION["gebruiker"]); // Haal de gebruiker uit de session
  $_SESSION["toast"] = 6;
  header("location:/login.php"); // Stuur de gebruiker door naar het login scherm
}