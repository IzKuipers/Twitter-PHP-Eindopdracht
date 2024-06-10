<?php

require_once ("gebruiker.php");

// Deze functie geeft een lijst terug met alle posts. Het voegt ook de eigenschappen van
// iedere auteur toe aan iedere tweet om het makkelijker te maken voor de HTML implementatie
function postsOphalen()
{
  // Maak verbinding met de database dmv verbindMysqli()
  $connectie = verbindMysqli();

  try { // Probeer...
    global $getStatement;

    // De vraag aan de database: Geef mij alle data van alle posts, met de nieuwste als eerste en de oudste als laatste (descending timestamp)
    $query = "SELECT * FROM posts ORDER BY timestamp DESC";

    $getStatement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $getStatement->execute(); // Voer de vraag uit 

    $result = array();

    // Schrijf voor iedere post de data naar de respectieve variabelen. Deze data zal veranderen iedere keer dat de fetch() functie wordt uitgevoerd, vandaar de while loop die volgt.
    $getStatement->bind_result($idPost, $auteur, $body, $likes, $timestamp);

    while ($getStatement->fetch()) { // Ga over alle tweets in het resultaat
      $gebruiker = gebruikerOphalen($auteur); // Verkrijg de eigenschappen van de auteur

      // Voeg de tweet+auteur toe aan de lijst met posts
      $result[] = array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp);
    }

    // Geef de array met alle "expanded" (als in iedere post + de auteur eigenschappen) terug
    return $result;
  } catch (Exception $e) { // Anders...
    return array(); // Geef een lege array terug als "dummy"
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten
    sluitMysqli($connectie, $getStatement);
  }
}

// Deze functie geeft de daadwerkelijke posts weer in de HTML
function postLijst()
{
  session_start(); // Start de sessie voor de ingelogde gebruiker's eigenschappen

  $posts = postsOphalen(); // Verkrijg alle posts met de nieuwste als eerste, en de oudste als laatste.
  $gebruiker = gebruikerUitSessie(); // Verkrijg de gebruiker uit de informatie die bij het inloggen is opgeslagen in de session

  echo "<div class='post-lijst'>"; // Open een DIV element met de class 'post-lijst'

  // Geef een melding weer als er geen tweets zijn.
  if (count($posts) == 0) {
    echo <<<HTML
      <p class="geen">
        <span class="material-icons-round">warning</span>
        <span class="bericht">De dood van het universum is hier! Er zijn geen posts. Zal jij de eerste tweet sturen?</span>
      </p>
    HTML;
  }

  // Voor iedere tweet in de array $posts, doe...
  foreach ($posts as $post) {
    $body = $post['body']; // De content van de tweet
    $bodyVeilig = htmlspecialchars($body); // De content van de tweet, beveiligd tegen XSS
    $id = $post['id']; // De ID van de tweet
    $aantal_likes = $post["likes"]; // De likes van de tweet
    $timestamp = date("j M · G:i", strtotime($post["timestamp"])); // Een nette datum en tijd die onder aan de post wordt weergegeven

    $postVanGebruiker = $gebruiker["id"] == $post["auteur"]["idGebruiker"]; // Een boolean die aangeeft of de post van de ingelogde gebruiker is
    $gebruikersnaam = $post['auteur']['naam'] . ($postVanGebruiker ? " (jij)" : ""); // De gebruikersnaam die boven de post wordt weergegeven

    // Een verwijder-knop die alleen wordt weergegeven als de post van de ingelogde gebruiker is. Die conditie wordt ook gecontroleerd in /verwijder.php
    $verwijderKnop = $postVanGebruiker ?
      <<<HTML
      <a href="/verwijder.php?id=$id" class="delete-button">
        <span class='material-icons-round'>delete</span>
        <span>Verwijder</span>
      </a>
      HTML : "";

    echo <<<HTML
      <!-- De div van dep post -->
      <div class='post'>
        <!-- De linker kant: hier wordt de profielfoto weergegeven (een standaard foto in dit project) -->
        <div class="left">
          <img src="/images/pfp.png" alt="">
        </div>
        <!-- De rechter kant: hier wordt de content van de tweet weergegeven -->
        <div class="right">
          <!-- Boven de content: De auteur's naam + de ID van de post -->
          <div class="auteur">
            <span class="naam">$gebruikersnaam</span>
            <span class="id">· Post #$id</span>
          </div>  
          <!-- De content van de post, beschermd tegen XSS  -->
          <div class="body">$bodyVeilig</div>
          <!-- Boven de content: De auteur's naam + de ID van de post -->
          <div class="actions">
            <!-- De knop om een post te "liken" -->
            <a href="/like.php?id=$id" class="like-button">
              <span class='material-icons-round'>favorite_outline</span>
              $aantal_likes
            </a>
            <!-- De verwijder knop. Deze variabele is een lege string ("") als de post niet van de ingelogde gebruiker is -->
            $verwijderKnop
            <!-- De geformatteerde datum en tijd van de post -->
            <div class="timestamp">
              $timestamp
            </div>
          </div>
        </div>
      </div>

    HTML;
  }

  echo "</div>";
}