<?php
header('Content-Type: application/json');
require_once __DIR__.'/../db.php';
$session = $_GET['session_id'] ?? '';
$limit = (int)($_GET['limit'] ?? 50);
$stmt=$conn->prepare('SELECT role,content FROM gf_messages WHERE session_id=? ORDER BY created_at DESC LIMIT ?');
$stmt->bind_param('si',$session,$limit);
$stmt->execute();
$res=$stmt->get_result();
$items=[]; while($row=$res->fetch_assoc()){ $items[]=$row; }
$stmt->close();
$items=array_reverse($items);
echo json_encode(['items'=>$items]);
?>
