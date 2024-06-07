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
  $posts = krijg_posts();

  echo "<div class='post-lijst'>";

  foreach ($posts as $post) {
    $body = $post['body'];
    $gebruikersnaam = $post['auteur']['naam'];
    $id = $post['id'];
    $aantal_likes = $post["likes"];
    $timestamp = $post["timestamp"];

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
            $body
          </div>
          <div class="actions">
            <a href="/like.php?id=$id" class="like-button">
              <span class='material-icons-round'>favorite_outline</span>
              $aantal_likes
            </a>
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