<?php
$host = "sql211.infinityfree.com";
$user = "if0_39515398";
$pass = "FAwpKlRJRp";
$db   = "if0_39515398_data";


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
