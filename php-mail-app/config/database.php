<?php
$host = "mysql";
$dbname = "app_db";
$username = "user";
$password = "password";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
