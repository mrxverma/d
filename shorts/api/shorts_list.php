<?php
header('Content-Type: application/json');
require_once '../db.php';
$page = max(1, (int)($_GET['page'] ?? 1));
$page_size = (int)($_GET['page_size'] ?? 10);
$page_size = $page_size > 20 ? 20 : $page_size;
$offset = ($page - 1) * $page_size;
$stmt = $conn->prepare('SELECT id, link, likes_count, time FROM shorts ORDER BY time DESC LIMIT ?, ?');
$stmt->bind_param('ii', $offset, $page_size);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();
// check if more items exist
$stmt = $conn->prepare('SELECT COUNT(*) as cnt FROM shorts');
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['cnt'];
$stmt->close();
$has_more = ($offset + $page_size) < $total;
echo json_encode(['items' => $items, 'has_more' => $has_more]);
?>
