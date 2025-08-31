<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
require_once __DIR__.'/../db.php';
$input = json_decode(file_get_contents('php://input'), true);
$session = $input['session_id'] ?? '';
$message = trim($input['message'] ?? '');
if($session==='' || $message===''){
  echo "data: [DONE]\n\n"; exit;
}
$personality = file_get_contents(__DIR__.'/../personality.md');
$stmt = $conn->prepare('SELECT role,content FROM gf_messages WHERE session_id=? ORDER BY created_at DESC LIMIT 20');
$stmt->bind_param('s',$session);
$stmt->execute();
$res = $stmt->get_result();
$history=[]; while($row=$res->fetch_assoc()){ $history[]=$row; }
$stmt->close();
$history=array_reverse($history);
$messages = [['role'=>'system','content'=>$personality]];
foreach($history as $h){ $messages[] = $h; }
$messages[]=['role'=>'user','content'=>$message];
$key = getenv('OPENAI_API_KEY');
$assistant='';
if($key){
  $ch = curl_init('https://api.openai.com/v1/chat/completions');
  curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
      'Content-Type: application/json',
      'Authorization: Bearer '.$key
    ],
    CURLOPT_POSTFIELDS=>json_encode([
      'model'=>'gpt-3.5-turbo',
      'messages'=>$messages,
      'temperature'=>0.6
    ]),
    CURLOPT_RETURNTRANSFER=>true
  ]);
  $resp = curl_exec($ch);
  curl_close($ch);
  $data = json_decode($resp,true);
  $assistant = $data['choices'][0]['message']['content'] ?? '';
  foreach(str_split($assistant,20) as $chunk){
    echo 'data: '.json_encode($chunk)."\n\n";
    @ob_flush(); flush();
  }
  echo "data: [DONE]\n\n";
} else {
  $assistant = strrev($message);
  echo 'data: '.json_encode($assistant)."\n\n";
  echo "data: [DONE]\n\n";
}
$stmt = $conn->prepare('INSERT INTO gf_messages(session_id,role,content) VALUES(?,?,?)');
$role='user'; $content=$message; $stmt->bind_param('sss',$session,$role,$content); $stmt->execute();
$role='assistant'; $content=$assistant; $stmt->bind_param('sss',$session,$role,$content); $stmt->execute();
$stmt->close();
?>
