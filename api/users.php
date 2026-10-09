<?php
require __DIR__.'/config.php';
$pdo=getPDO();
$method=$_SERVER['REQUEST_METHOD'];
if($method==='GET'){
  requireAuth(['admin']);
  $rows=$pdo->query("SELECT id,username,nama,role FROM users ORDER BY rowid ASC")->fetchAll(PDO::FETCH_ASSOC);
  json($rows);
}
requireAuth(['admin']);
if($method==='POST'){
  $b=body();
  $u=trim($b['username']??''); $n=trim($b['nama']??$u); $p=$b['password']??''; $r=$b['role']??'editor';
  if(!preg_match('/^[a-zA-Z0-9_.-]{3,32}$/',$u)) json(['error'=>'Username 3-32 char, huruf/angka/._- saja'],422);
  if(strlen($p)<6||strlen($p)>72) json(['error'=>'Password 6-72 char'],422);
  if(strlen($n)>80) json(['error'=>'Nama terlalu panjang'],422);
  if(!in_array($r,['admin','editor'])) $r='editor';
  $id=genId(); $hash=password_hash($p,PASSWORD_DEFAULT);
  try{
    $pdo->prepare("INSERT INTO users(id,username,nama,pass,role) VALUES(?,?,?,?,?)")->execute([$id,$u,$n,$hash,$r]);
  }catch(PDOException $e){ json(['error'=>'Username sudah ada'],409); }
  json(['ok'=>true,'id'=>$id]);
}
if($method==='PUT' || $method==='PATCH'){
  $b=body(); $id=$b['id']??''; 
  if(!$id) json(['error'=>'id required'],400);
  $sets=[]; $vals=[];
  if(isset($b['nama'])){ $v=trim($b['nama']); if(strlen($v)>80) json(['error'=>'Nama terlalu panjang'],422); $sets[]="nama=?"; $vals[]=mb_substr($v,0,80); }
  if(isset($b['role']) && in_array($b['role'],['admin','editor'])){
    if($id===$_SESSION['user']['id'] && $b['role']!=='admin') json(['error'=>'Tidak bisa downgrade diri sendiri'],400);
    $sets[]="role=?"; $vals[]=$b['role'];
    if($id===$_SESSION['user']['id']) $_SESSION['user']['role']=$b['role'];
  }
  if(array_key_exists('password',$b) && $b['password']!==''){
    if(strlen($b['password'])<6||strlen($b['password'])>72) json(['error'=>'Password 6-72 char'],422);
    $sets[]="pass=?"; $vals[]=password_hash($b['password'],PASSWORD_DEFAULT);
  }
  if(!$sets) json(['error'=>'Nothing to update'],400);
  $vals[]=$id;
  $pdo->prepare("UPDATE users SET ".implode(',',$sets)." WHERE id=?")->execute($vals);
  json(['ok'=>true]);
}
if($method==='DELETE'){
  $id=$_GET['id']??body()['id']??'';
  if(!$id) json(['error'=>'id required'],400);
  if($id===$_SESSION['user']['id']) json(['error'=>'Tidak bisa hapus diri sendiri'],400);
  // prevent deleting last admin
  $adminCount=$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
  $targetRole=$pdo->query("SELECT role FROM users WHERE id=".$pdo->quote($id))->fetchColumn();
  if($targetRole==='admin' && $adminCount<=1) json(['error'=>'Tidak bisa hapus admin terakhir'],400);
  $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
  json(['ok'=>true]);
}
json(['error'=>'Method not allowed'],405);
