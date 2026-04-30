<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "sql213.infinityfree.com"; 
$user = "if0_41400485"; 
$pass = "RAYYAN99977"; 
$dbname = "if0_41400485_nura_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>