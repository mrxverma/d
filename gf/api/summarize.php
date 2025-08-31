<?php
header('Content-Type: application/json');
require_once __DIR__.'/../db.php';
$input=json_decode(file_get_contents('php://input'),true);
$text=$input['text']??'';
$key=$see;
if($key && $text){
  $prompt="Extract any durable preference, fact or goal worth remembering in ≤ 1 sentence. If none, output null.\n\n".$text;
  $ch = curl_init('https://api.openai.com/v1/chat/completions');
  curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
      'Content-Type: application/json',
      'Authorization: Bearer '.$key
    ],
    CURLOPT_POSTFIELDS=>json_encode([
      'model'=>'gpt-3.5-turbo',
      'messages'=>[['role'=>'user','content'=>$prompt]],
      'temperature'=>0.3
    ]),
    CURLOPT_RETURNTRANSFER=>true
  ]);
  $resp=curl_exec($ch); curl_close($ch);
  $data=json_decode($resp,true);
  $memory=$data['choices'][0]['message']['content']??'null';
  echo json_encode(['memory'=>$memory]);
}else{
  echo json_encode(['memory'=>null]);
}
?>
