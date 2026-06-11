<?php
mysqli_report(MYSQLI_REPORT_OFF);
$host = "localhost";
$port = 3307;
$user = "root";
$pass = "";
$dbname = "jibika_db";

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>