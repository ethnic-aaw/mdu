<?php
require __DIR__.'/config.php';
$pdo=getPDO();
$method=$_SERVER['REQUEST_METHOD'];
if($method==='POST'){
  $ip=$_SERVER['REMOTE_ADDR']??'unknown';
  throttle("inbox:$ip", 5, 60); // 5/min
  $b=body();
  $nama=trim($b['nama']??''); $kontak=trim($b['kontak']??''); $pesan=trim($b['pesan']??''); $subjek=trim($b['subjek']??'Lainnya');
  if($nama===''||$kontak===''||strlen($pesan)<10) json(['error'=>'Validasi gagal'],422);
  if(strlen($nama)>120||strlen($kontak)>80||strlen($pesan)>2000||strlen($subjek)>80) json(['error'=>'Input terlalu panjang'],422);
  $nama=mb_substr($nama,0,120); $kontak=mb_substr($kontak,0,80); $pesan=mb_substr($pesan,0,2000); $subjek=mb_substr($subjek,0,80);
  $allowedSub=['Pendaftaran Santri Baru','Informasi Biaya','Kunjungan Sekolah','Lainnya'];
  if(!in_array($subjek,$allowedSub)) $subjek='Lainnya';
  $id=genId(); $tgl=date('Y-m-d');
  // handle both column names
  try{
    $pdo->prepare("INSERT INTO inbox(id,nama,kontak,subjek,pesan,tgl,is_read) VALUES(?,?,?,?,?,?,0)")->execute([$id,$nama,$kontak,$subjek,$pesan,$tgl]);
  }catch(PDOException $e){
    $pdo->prepare("INSERT INTO inbox(id,nama,kontak,subjek,pesan,tgl,\"read\") VALUES(?,?,?,?,?,?,0)")->execute([$id,$nama,$kontak,$subjek,$pesan,$tgl]);
  }
  json(['ok'=>true,'id'=>$id]);
}
if($method==='GET'){
  requireAuth(['admin','editor']);
  // be tolerant of column name
  try{
    $rows=$pdo->query("SELECT * FROM inbox ORDER BY tgl DESC, rowid DESC")->fetchAll(PDO::FETCH_ASSOC);
  }catch(PDOException $e){
    $rows=$pdo->query("SELECT * FROM inbox ORDER BY tgl DESC, rowid DESC")->fetchAll(PDO::FETCH_ASSOC);
  }
  // normalize read flag
  foreach($rows as &$r){ $r['is_read']=$r['is_read']??$r['read']??0; }
  json($rows);
}
if($method==='PATCH'){
  requireAuth();
  $b=body(); $id=$b['id']??''; 
  if(!$id) json(['error'=>'id required'],400);
  try{ $pdo->prepare("UPDATE inbox SET is_read=1 WHERE id=?")->execute([$id]); }
  catch(PDOException $e){ $pdo->prepare("UPDATE inbox SET \"read\"=1 WHERE id=?")->execute([$id]); }
  json(['ok'=>true]);
}
if($method==='DELETE'){
  requireAuth();
  $id=$_GET['id']??body()['id']??'';
  if(!$id) json(['error'=>'id required'],400);
  $pdo->prepare("DELETE FROM inbox WHERE id=?")->execute([$id]);
  json(['ok'=>true]);
}
json(['error'=>'Method not allowed'],405);
