<?php
$conn = new mysqli('localhost', 'root', '', 'cs174');

if ($conn->connect_error)
{
    die($conn->connect_error);
}


?>
