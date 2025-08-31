<?php
header('Content-Type: application/json');
require_once __DIR__.'/../db.php';
$db = $conn->ping() ? 'ok' : 'fail';
$key = getenv('OPENAI_API_KEY');
$openai = $key ? 'ok' : 'missing';
$personality = file_exists(__DIR__.'/../personality.md') ? 'loaded' : 'missing';
echo json_encode(['db'=>$db,'openai'=>$openai,'personality'=>$personality]);
?>
