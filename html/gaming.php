<!DOCTYPE html>
<html>
  <head>
    <title>Gaming Profiles</title>
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
  </head>
  <body>
    <ul class="navbar">
      <li><a href="index.html">Home</a></li>
      <li><a class="active" href="gaming.php">Gaming</a></li>
      <li style="float:right"><a href="about.html">About</a></li>
    </ul>
    <div class="wrapper">
    <h1 class="title">Gaming Profiles</h1>
    <div>
      <p>I am currently in the process of writing python programs to retrieve data from various API's and would like to display them here eventually. The first one I'm working on is Steam. Below you can find the output as I have created so far.</p>
    </div>
    <div onclick="hideToggle()" style="cursor:pointer">
      <p>Steps I used to create this page: <span id="rightArrow"> &#9205; </span> <span id="downArrow" style="display:none"> &#9207; </span></p>
    </div>
    <div id="toggle" style="display:none">
      <ul>
        <li>Create a Python script to pull data from the Steam Web API and store it into a MySQL database. The main problems I had with this step were:
          <ul>
            <li>Surrounding the variables with quotes so my insert statements would work correctly. I couldn't figure out the problem with the error message that was being output. (An example of this issue: <b><i>INSERT INTO games(name, image) VALUES "{0}", "{1}"</i></b>. Without the quotations my database would throw an error like <b><i>"Unknown column 'Eets' in 'field list'"</i></b> which was pretty nonsensical.</li>
            <li>MySQL defaulting to latin-1 which threw errors inserting games with certain characters in the name. Figuring out how to convert the database to utf8 and ensuring further connections use utf8 was quite painful.</li>
          </ul>
        </li>
        <li>Set up a cron job to run the script daily at midnight to update the game list, playtimes, and recent games.
          <ul>
            <li>Sometimes my script wouldn't run to update my game times. Turns out I had a deadlock with a differen't script trying to access my database. Staggering the scripts to run at slightly different times resolves the deadlock issue</li>
          </ul>
        </li>
        <li>Use PHP to extract the data from my database and insert it onto the page as HTML. The problem I encountered here was:
          <ul> 
            <li>Using the thumbnail urls from steam were not secure (https) therefor making this page unsecure as well. I couldn't figure out why my page had lost its secure lock until I remembered the thumbnails. Solution to this was to download them and reference them internally instead of referencing the steam url for the images as I orginally was doing.</li>
          </ul>
        </li> 
      </ul>
    </div>
    <div>
      <input type="text" id="searchBox" onkeyup="filterGames()" placeholder="Search Games">
    </div>
    <div>
      <table id="gameTable"> 
        <tr class="heading"> 
          <th colspan="2">Game</th>
          <th>Total Playtime</th> 
          <th>Playtime in 2 weeks</th>
        </tr>
<?php
$variables = array();
$lines = file("resources/config.txt");
foreach($lines as $line) {
  $temp = explode('=', $line, 2);
  $variables[$temp[0]] = trim($temp[1]);
}

$db_db = $variables["dbdb"];
$db_host = $variables["dbhost"];
$db_user= $variables["dbuser"];
$db_pw= $variables["dbpw"];

$pdo = new PDO("mysql:host=$db_host;dbname=$db_db;charset=utf8mb4", $db_user, $db_pw);
$statement = $pdo->query("SELECT * FROM steam_games ORDER BY playtime_forever DESC");
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
  if ($row["name"] == "Black Desert Online") {
    continue;
  }
  $image = $row["appid"];
  if (is_null($row["img_icon_path"])) {
    $image = "none";
  }
  $name = $row["name"];
  $playtime_forever_hours = floor($row["playtime_forever"]/60);
  $playtime_forever_minutes = $row["playtime_forever"]%60;
  $playtime_2weeks_hours = floor($row["playtime_2weeks"]/60);
  $playtime_2weeks_minutes = $row["playtime_2weeks"]%60;
  echo '<tr> 
    <td class="image">
    <img src="resources/thumbnails/'.$image.'.jpg" alt="'.$name.'" height="40" width="40">
    </td>
    <td class="name">'.$name.'</td>';
  if ($playtime_forever_hours > 0) {
    echo '<td class="playtime_forever">'.$playtime_forever_hours.' hours and '.$playtime_forever_minutes.' minutes</td>';
  } else if ($playtime_forever_minutes > 0) {
    echo '<td class="playtime_forever">'.$playtime_forever_minutes.' minutes</td>';
  } else {
    echo '<td class="playtime_forever"></td>';
  }
  if ($playtime_2weeks_hours > 0) {
    echo '<td class="playtime_2weeks">'.$playtime_2weeks_hours.' hours and '.$playtime_2weeks_minutes.' minutes</td>';
  } else if ($playtime_2weeks_minutes > 0) {
    echo '<td class="playtime_2weeks">'.$playtime_2weeks_minutes.' minutes</td>';
  } else {
    echo '<td class="playtime_2weeks"></td>';
  }
  echo '</tr>
';
}
?>
      </table>
    </div>
  </div>
  <script src="resources/js/functions.js"></script>
  </body>
</html>
