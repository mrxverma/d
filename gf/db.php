<?php
$host = "sql211.infinityfree.com";
$user = "if0_39515398";
$pass = "FAwpKlRJRp";
$db   = "if0_39515398_data";
$see = "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";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
