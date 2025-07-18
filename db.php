<?php
session_start();

$host = getenv("DB_HOST") ?: "localhost";
$port = getenv("DB_PORT") ?: 3306;
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "";
$dbname = getenv("DB_NAME") ?: "Booking";

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
