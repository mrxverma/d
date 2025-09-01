<?php
header('Content-Type: application/json');
require_once '../db.php';
$short_id = (int)($_GET['short_id'] ?? 0);
$limit = (int)($_GET['limit'] ?? 50);
if (!$short_id) { echo json_encode(['items'=>[]]); exit; }
$stmt = $conn->prepare('SELECT id, username, comment, time FROM comments WHERE short_id = ? ORDER BY time DESC LIMIT ?');
$stmt->bind_param('ii', $short_id, $limit);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while ($row = $result->fetch_assoc()) {
    $row['username'] = htmlspecialchars($row['username']);
    $row['comment'] = htmlspecialchars($row['comment']);
    $items[] = $row;
}
$stmt->close();
echo json_encode(['items' => $items]);
?>
