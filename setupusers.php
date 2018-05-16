<?php // setupusers.php

include_once 'db.php';
if ($conn->connect_error) die($conn->connect_error);

$query = "CREATE TABLE users (forename VARCHAR(32) NOT NULL,surname VARCHAR(32) NOT NULL,username VARCHAR(32) NOT NULL UNIQUE,password VARCHAR(32) NOT NULL, salt1 VARCHAR(10) NOT NULL, salt2 VARCHAR(10) NOT NULL)";
$result = $conn->query($query);if (!$result) die($connection->error);
$salt1 = "qm&h*";
$salt2 = "pg!@";
$forename = 'Joanitha';$surname = 'Christle';
$username = 'admin';$password = 'letmein';
$token = hash('ripemd128', "$salt1$password$salt2");

add_user($conn, $forename, $surname, $username, $token, $salt1, $salt2);


function add_user($connection, $fn, $sn, $un, $pw,$salt1, $salt2) {
  $query = "INSERT INTO users VALUES('$fn', '$sn', '$un', '$pw','$salt1', '$salt2')";
  $result = $connection->query($query);
  if (!$result) die($connection->error);
}

?>
