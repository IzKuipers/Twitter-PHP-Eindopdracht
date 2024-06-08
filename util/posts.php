<?php

require_once ("gebruiker.php");

function krijg_posts()
{
  $connectie = verbind_mysqli();

  // try {
  // global $statement;

  $query = "SELECT * FROM posts ORDER BY timestamp DESC";
  $statement = $connectie->prepare($query);
  $statement->execute();

  $result = array();

  $statement->bind_result($idPost, $auteur, $body, $likes, $timestamp);

  while ($statement->fetch()) {
    $gebruiker = gebruiker_ophalen($auteur);

    $result[] = array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp);
  }
  sluit_mysqli($connectie);

  return $result;
  // } catch (Exception $e) {
  //   return array();
  // } finally {
  // }
}

function post_ophalen($id)
{
  $connectie = verbind_mysqli();
  $query = "SELECT * FROM posts ORDER BY timestamp DESC WHERE id=?";

  try {

    $statement = $connectie->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->fetch();
    $statement->bind_result($idPost, $auteur, $body, $likes, $timestamp);

    $gebruiker = gebruiker_ophalen($auteur);

    return array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp);
  } catch (Exception $e) {
    return array("id" => -1, "auteur" => "", "body" => "", "likes" => -1, "timestamp" => -1);
  } finally {
    sluit_mysqli($connectie, $statement);
  }
}

function post_lijst()
{
  session_start();

  $posts = krijg_posts();
  $gebruiker = gebruiker_uit_sessie();

  echo "<div class='post-lijst'>";

  if (count($posts) == 0) {
    echo <<<HTML
      <p class="geen">
        <span class="material-icons-round">warning</span>
        <span class="bericht">De dood van het universum is hier! Er zijn geen posts. Zal jij de eerste tweet sturen?</span>
      </p>
    HTML;
  }

  foreach ($posts as $post) {
    $body = $post['body'];
    $bodyVeilig = htmlspecialchars($body);
    $id = $post['id'];
    $aantal_likes = $post["likes"];
    $timestamp = date("j M · G:i", strtotime($post["timestamp"]));

    $postVanGebruiker = $gebruiker["id"] == $post["auteur"]["idGebruiker"];
    $gebruikersnaam = $post['auteur']['naam'] . ($postVanGebruiker ? " (jij)" : "");

    $verwijderKnop = $gebruiker["id"] == $post["auteur"]["idGebruiker"] ?
      <<<HTML
      <a href="/verwijder.php?id=$id" class="delete-button">
        <span class='material-icons-round'>delete</span>
        Verwijder
      </a>
      HTML : "";

    echo <<<HTML
      <div class='post'>
        <div class="left">
          <img src="/images/pfp.png" alt="">
        </div>
        <div class="right">
          <div class="auteur">
            <span class="naam">$gebruikersnaam</span>
            <span class="id">· Post #$id</span>
          </div>  
          <div class="body">
            $bodyVeilig
          </div>
          <div class="actions">
            <a href="/like.php?id=$id" class="like-button">
              <span class='material-icons-round'>favorite_outline</span>
              $aantal_likes
            </a>
            $verwijderKnop
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