<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'user_management';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
