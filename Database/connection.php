<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'etickects');

if (!$conn) {
    echo "Database not Connected, Try again!";
}
?>
