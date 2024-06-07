<?php

require ("gebruiker.php");

function krijg_posts()
{
  $connectie = verbind_mysqli();

  try {
    global $statement;

    $query = "SELECT * FROM posts ORDER BY timestamp DESC";
    $statement = $connectie->prepare($query);
    $statement->execute();
  
    $result = array();
  
    $statement->bind_result($idPost, $auteur, $body, $likes, $timestamp);
  
    while ($statement->fetch()) {
      $gebruiker = gebruiker_ophalen($auteur);
  
      $result[] = array("id" => $idPost, "auteur" => $gebruiker, "body" => $body, "likes" => $likes, "timestamp" => $timestamp);
    }
  
    return $result;
  } catch (Exception $e) {
    return array();
  } finally {
    sluit_mysqli($connectie, $statement);
  }
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
    foutmelding()
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
    $aantal_likes = $post["likes"];
    $timestamp = $post["timestamp"];

    echo <<<HTML

      <div class='post'>
        <div class="auteur">
          $gebruikersnaam
        </div>  
        <div class="body">
          $body
        </div>
        <div class="actions">
          <a href="/like.php">
            <span class='material-icons-round'>heart</span>
            $aantal_likes
          </a>
          <div class="right">
            $timestamp
          </div>
        </div>
    </div>

    HTML;
  }

  echo "</div>";
}