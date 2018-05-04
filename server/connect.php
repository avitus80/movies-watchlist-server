<?php
  $host = 'localhost';
  $username = "aswebdev_admin";
  $password = "admin1234";
  $dbname = "aswebdev_movies_watchlist";
  $port = 3306;

  $mysqli = new mysqli($host, $username, $password, $dbname, $port);

  if($mysqli === false) {
    die("ERROR: Could not connect. " . $mysqli->connect_error);
  }
?>