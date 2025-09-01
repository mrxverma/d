<?php
header('Content-Type: application/json');
require_once __DIR__.'/../db.php';
$method = $_SERVER['REQUEST_METHOD'];
if($method==='GET'){
  $q = $_GET['q'] ?? '';
  if($q){
    $stmt=$conn->prepare('SELECT id,type,value FROM gf_memories WHERE value LIKE CONCAT("%", ?, "%") ORDER BY last_used DESC');
    $stmt->bind_param('s',$q);
  }else{
    $stmt=$conn->prepare('SELECT id,type,value FROM gf_memories ORDER BY last_used DESC');
  }
  $stmt->execute();
  $res=$stmt->get_result();
  $items=$res->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  echo json_encode(['items'=>$items]);
}elseif($method==='POST'){
  $data=json_decode(file_get_contents('php://input'),true);
  $type=$data['type']??'';
  $value=trim($data['value']??'');
  $session=$data['session_id']??null;
  if(!$type||$value===''){echo json_encode(['error'=>'invalid']);exit;}
  $stmt=$conn->prepare('INSERT INTO gf_memories(session_id,type,value,created_at) VALUES(?,?,?,NOW())');
  $stmt->bind_param('sss',$session,$type,$value);
  $stmt->execute();
  $id=$stmt->insert_id;
  $stmt->close();
  echo json_encode(['ok'=>true,'id'=>$id]);
}elseif($method==='DELETE'){
  $id=(int)($_GET['id']??0);
  if(!$id){echo json_encode(['error'=>'id']);exit;}
  $stmt=$conn->prepare('DELETE FROM gf_memories WHERE id=?');
  $stmt->bind_param('i',$id);
  $stmt->execute();
  $stmt->close();
  echo json_encode(['ok'=>true]);
}else{
  http_response_code(405);
}
?>
