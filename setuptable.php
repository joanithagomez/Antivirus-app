<?php

include_once 'db.php';

if($conn->query("DESCRIBE `malwares`")) {
    echo "<br>malwares table exists. ";
}else{
  echo "<br>Creating 'malwares' table.";
  $query = "CREATE TABLE malwares (id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,name VARCHAR(32) NOT NULL,contents VARCHAR(250),PRIMARY KEY (id))";
  $result = $conn->query($query);
  if (!$result) die($conn->error);
}


if($conn->query("DESCRIBE `users`")) {
    echo "<br>users table exists. ";
}else{
  echo "<br>Creating 'users' table with 1 admin. username: admin and password: letmein";
  include_once 'setupusers.php';
}
?>
