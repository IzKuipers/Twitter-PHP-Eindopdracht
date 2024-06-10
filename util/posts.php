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
    $query = "SELECT * FROM posts WHERE repliesTo IS NULL ORDER BY timestamp DESC";

    $getStatement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $getStatement->execute(); // Voer de vraag uit 

    $result = array();

    // Schrijf voor iedere post de data naar de respectieve variabelen. Deze data zal veranderen iedere keer dat de fetch() functie wordt uitgevoerd, vandaar de while loop die volgt.
    $getStatement->bind_result($idPost, $auteur, $body, $likes, $timestamp, $reageertOp);

    while ($getStatement->fetch()) { // Ga over alle tweets in het resultaat
      $gebruiker = gebruikerOphalen($auteur); // Verkrijg de eigenschappen van de auteur
      $reacties = reactiesVanPost($idPost); // Verkrijg de reacties van de post

      // Voeg de tweet+auteur toe aan de lijst met posts
      $result[] = array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp, "reacties" => $reacties);
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

function reactiesVanPost($id)
{

  // Maak verbinding met de database dmv verbindMysqli()
  $connectie = verbindMysqli();

  try { // Probeer...
    // De vraag aan de database: Geef mij alle data van alle posts, met de nieuwste als eerste en de oudste als laatste (descending timestamp)
    $query = "SELECT * FROM posts WHERE repliesTo = ? ORDER BY timestamp DESC";

    $getStatement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $getStatement->bind_param("i", $id); // Vervang het vraagteken met de daadwerkelijke ID
    $getStatement->execute(); // Voer de vraag uit 

    $result = array();

    // Schrijf voor iedere post de data naar de respectieve variabelen. Deze data zal veranderen iedere keer dat de fetch() functie wordt uitgevoerd, vandaar de while loop die volgt.
    $getStatement->bind_result($idPost, $auteur, $body, $likes, $timestamp, $reageertOp);

    while ($getStatement->fetch()) { // Ga over alle tweets in het resultaat
      $gebruiker = gebruikerOphalen($auteur); // Verkrijg de eigenschappen van de auteur

      $reacties = reactiesVanPost($idPost);

      // Voeg de tweet+auteur toe aan de lijst met posts
      $result[] = array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp, "reacties" => $reacties);
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

function postsVanGebruiker($id)
{
  // Maak verbinding met de database dmv verbindMysqli()
  $connectie = verbindMysqli();

  try { // Probeer...
    // De vraag aan de database: Geef mij alle data van alle posts, met de nieuwste als eerste en de oudste als laatste (descending timestamp)
    $query = "SELECT * FROM posts WHERE auteur = ? ORDER BY timestamp DESC";

    $getPostsStatement = $connectie->prepare($query); // Bereid de vraag aan de database voor
    $getPostsStatement->bind_param("i", $id);
    $getPostsStatement->execute(); // Voer de vraag uit 

    $result = array();

    // Schrijf voor iedere post de data naar de respectieve variabelen. Deze data zal veranderen iedere keer dat de fetch() functie wordt uitgevoerd, vandaar de while loop die volgt.
    $getPostsStatement->bind_result($idPost, $auteur, $body, $likes, $timestamp, $reageertOp);

    while ($getPostsStatement->fetch()) { // Ga over alle tweets in het resultaat
      $gebruiker = gebruikerOphalen($auteur); // Verkrijg de eigenschappen van de auteur

      $reacties = reactiesVanPost($idPost);

      // Voeg de tweet+auteur toe aan de lijst met posts
      $result[] = array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp, "reacties" => $reacties);
    }

    // Geef de array met alle "expanded" (als in iedere post + de auteur eigenschappen) terug
    return $result;
  } catch (Exception $e) { // Anders...
    return array(); // Geef een lege array terug als "dummy"
  } finally { // En ten slotte...
    // Probeer de connectie en statement te sluiten
    sluitMysqli($connectie, $getPostsStatement);
  }
}

// Deze functie geeft de daadwerkelijke posts weer in de HTML
function postLijst($posts, $geenReacties = false)
{
  session_start(); // Start de sessie voor de ingelogde gebruiker's eigenschappen

  $gebruiker = gebruikerUitSessie(); // Verkrijg de gebruiker uit de informatie die bij het inloggen is opgeslagen in de session

  echo <<<HTML
    <script>
      function reactieFormulier(id) {
        const formulier = document.getElementById(id);

        if (!formulier) return;

        formulier.classList.toggle("zichtbaar");
      }
    </script>
  HTML;

  echo "<div class='post-lijst'>"; // Open een DIV element met de class 'post-lijst'

  // Geef een melding weer als er geen tweets zijn.
  if (count($posts) == 0) {
    echo <<<HTML
      <p class="geen">
        <span class="material-icons-round">warning</span>
        <span class="bericht">Hier zijn geen tweets! Wat een leegte...</span>
      </p>
    HTML;
  }

  // Voor iedere tweet in de array $posts, doe...
  foreach ($posts as $post) {
    if ($geenReacties) {
      echo genereerMinimalePostHtml($post, $gebruiker);
    } else {
      echo genereerPostHtml($post, $gebruiker);
    }
  }

  echo "</div>";
}

function genereerMinimalePostHtml($post, $gebruiker)
{
  $body = $post['body']; // De content van de tweet
  $bodyVeilig = htmlspecialchars($body); // De content van de tweet, beveiligd tegen XSS
  $id = $post['id']; // De ID van de tweet
  $aantal_likes = $post["likes"]; // De likes van de tweet
  $timestamp = date("j M · G:i", strtotime($post["timestamp"])); // Een nette datum en tijd die onder aan de post wordt weergegeven

  $gebruikerId = $post["auteur"]["idGebruiker"];
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

  $resultaat = "";
  $resultaat .= <<<HTML
    <div class='post'>
      <div class="post-content">
      <!-- De linker kant: hier wordt de profielfoto weergegeven (een standaard foto in dit project) -->
        <div class="left">
          <img src="/images/pfp.png" alt="">
        </div>
        <!-- De rechter kant: hier wordt de content van de tweet weergegeven -->
        <div class="right">
          <!-- Boven de content: De auteur's naam + de ID van de post -->
          <div class="auteur">
            <span class="naam"><a href="/profiel.php?id=$gebruikerId">$gebruikersnaam</a></span>
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
    </div>
    HTML;

  return $resultaat;

}

function genereerPostHtml($post, $gebruiker, $isReactie = false)
{
  $body = $post['body']; // De content van de tweet
  $bodyVeilig = htmlspecialchars($body); // De content van de tweet, beveiligd tegen XSS
  $id = $post['id']; // De ID van de tweet
  $aantal_likes = $post["likes"]; // De likes van de tweet
  $timestamp = date("j M · G:i", strtotime($post["timestamp"])); // Een nette datum en tijd die onder aan de post wordt weergegeven

  $gebruikerId = $post["auteur"]["idGebruiker"];
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

  $reactieForm = reactieFormulier($post);

  $classNaam = "post " . ($isReactie ? "reactie" : "");
  $resultaat = "<div class='$classNaam'>";
  $resultaat .= <<<HTML
    <div class="post-content">
    <!-- De linker kant: hier wordt de profielfoto weergegeven (een standaard foto in dit project) -->
      <div class="left">
        <img src="/images/pfp.png" alt="">
      </div>
      <!-- De rechter kant: hier wordt de content van de tweet weergegeven -->
      <div class="right">
        <!-- Boven de content: De auteur's naam + de ID van de post -->
        <div class="auteur">
          <span class="naam"><a href="/profiel.php?id=$gebruikerId">$gebruikersnaam</a></span>
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
          <!-- De knop om te reageren op een post -->
          <a href="javascript:reactieFormulier('reactieForm_$id')">
            <span class="material-icons-round">chat_bubble_outline</span>
          </a>
          <!-- De geformatteerde datum en tijd van de post -->
          <div class="timestamp">
            $timestamp
          </div>
        </div>
      </div>
    </div>
    <div class="post-reacties">
      $reactieForm
    HTML;

  // Voor elke reactie van de post...
  foreach ($post["reacties"] as $reactie) {
    $resultaat .= genereerPostHtml($reactie, $gebruiker, true); // Voeg alle reacties toe aan de HTML
  }

  // Sluit de post-content en post-reacties divs
  $resultaat .= "</div></div>";

  // Stuur de resulterende HTML terug
  return $resultaat;
}

function reactieFormulier($post)
{
  $id = $post["id"]; // De ID van de post
  $auteurnaam = $post["auteur"]["naam"];

  return <<<HTML
    <!-- Dit is een POST form die wordt verstuurd naar /stuurpost.php, met een hidden value die de reactie-id bevat -->
    <form class="reactie-form" method="POST" action="/stuurpost.php" id="reactieForm_$id">
      <input type="hidden" name="reactieOp" value="$id">
      <input type="text" placeholder="Wat heb je te zeggen?" name="bericht" value="@$auteurnaam " required maxlength="256" rows="2"></textarea>
      <!-- De knop om de post te versturen -->
      <input type="submit" value="Reageer">
    </form>
  HTML;
}

// Deze functie wordt gebruikt om het totaal aantal likes uit een lijst van posts te halen
function totaleLikes($posts)
{
  $likes = 0;

  foreach ($posts as $post) {
    $likes += $post["likes"];
  }

  return $likes;
}