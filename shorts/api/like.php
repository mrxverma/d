<?php
header('Content-Type: application/json');
require_once '../db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST required']);
    exit;
}
$short_id = (int)($_POST['short_id'] ?? 0);
if (!$short_id) { echo json_encode(['error' => 'Invalid id']); exit; }
$stmt = $conn->prepare('UPDATE shorts SET likes_count = likes_count + 1 WHERE id = ?');
$stmt->bind_param('i', $short_id);
$stmt->execute();
$stmt->close();
$stmt = $conn->prepare('SELECT likes_count FROM shorts WHERE id = ?');
$stmt->bind_param('i', $short_id);
$stmt->execute();
$likes = $stmt->get_result()->fetch_assoc()['likes_count'] ?? 0;
$stmt->close();
echo json_encode(['ok' => true, 'likes_count' => (int)$likes]);
?>
