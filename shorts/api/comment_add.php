<?php
header('Content-Type: application/json');
require_once '../db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST required']);
    exit;
}
$short_id = (int)($_POST['short_id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$comment = trim($_POST['comment'] ?? '');
if (!$short_id || $username === '' || $comment === '') {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}
if (strlen($username) > 40 || strlen($comment) > 300) {
    echo json_encode(['error' => 'Too long']);
    exit;
}
$stmt = $conn->prepare('INSERT INTO comments(short_id, username, comment, time) VALUES (?,?,?, NOW())');
$stmt->bind_param('iss', $short_id, $username, $comment);
$stmt->execute();
$id = $stmt->insert_id;
$stmt->close();
$stmt = $conn->prepare('SELECT id, username, comment, time FROM comments WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$row['username'] = htmlspecialchars($row['username']);
$row['comment'] = htmlspecialchars($row['comment']);
echo json_encode($row);
?>
