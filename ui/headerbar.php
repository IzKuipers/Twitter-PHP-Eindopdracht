<?php

function HeaderBar($gebruiker)
{
  $naam = $gebruiker["naam"];

  echo <<<HTML
  <link rel="stylesheet" href="/css/header.css">
  <header>
    <!-- Het Twitter merk met een vleugje PHP -->
    <div class="left">
      <img src="/images/logo.png" alt="">
      <h1><a href="/">Twitter<span class="sub">(PHP)</span></a></h1>
    </div>
    <!-- Aan de rechter kant: De gebruikersnaam met een knop om uit te loggen-->
    <div class="right">
      <div class="user">
        <!-- Het ?= teken is een kortere versie van ? echo -->
        <p class="username">$naam</p>
        <!-- We gebruiken /uitloggen.php voor het uitloggen-proces -->
        <a href="/uitloggen.php" class="material-icons-round">logout</a>
      </div>
    </div>
  </header>
  
  HTML;
}