<?php
require __DIR__.'/config.php';
$allowed = [
  'berita'    => ['judul','excerpt','img','tgl','tag','tagClass'],
  'program'   => ['icon','judul','desc','feat'],
  'fasilitas' => ['judul','desc','img','icon','wide'],
  'ekskul'    => ['judul','desc','icon','color'],
  'prestasi'  => ['judul','level','lokasi','kat','img'],
  'testimoni' => ['nama','peran','foto','teks'],
];
$table=$_GET['table']??'';
if(!isset($allowed[$table])) json(['error'=>'Invalid table'],400);
$pdo=getPDO();
$method=$_SERVER['REQUEST_METHOD'];
$colsAllowed=$allowed[$table];

// GET list — public, but safe
if($method==='GET'){
  $rows=$pdo->query("SELECT * FROM $table ORDER BY rowid DESC")->fetchAll(PDO::FETCH_ASSOC);
  json($rows);
}
requireAuth(['admin','editor']);

function filterBody($b,$colsAllowed){
  $out=[];
  foreach($colsAllowed as $c){ if(array_key_exists($c,$b)) $out[$c]=$b[$c]; }
  return $out;
}

// POST create
if($method==='POST'){
  $b=body();
  $filtered=filterBody($b,$colsAllowed);
  $hasRequired = isset($b['judul']) || isset($b['nama']);
  if(!$hasRequired && empty($filtered)) json(['error'=>'Judul/Nama wajib'],422);
  // validate required field present
  $need = in_array($table,['testimoni']) ? 'nama' : 'judul';
  if(empty(trim($filtered[$need]??''))) json(['error'=>"$need wajib"],422);
  // validate kat / color / url bounds
  if(isset($filtered['kat']) && !in_array($filtered['kat'],['akademik','seni','olahraga'])) json(['error'=>'Kategori tidak valid'],422);
  if(isset($filtered['color']) && !preg_match('/^#[0-9a-fA-F]{6}$/',$filtered['color'])) $filtered['color']='#0F5132';
  if(isset($filtered['icon'])) $filtered['icon']=preg_replace('/[^a-z0-9-]/','', $filtered['icon']);
  if(isset($filtered['wide'])) $filtered['wide']= $filtered['wide']==='wide' ? 'wide' : '';
  if(isset($filtered['feat'])) $filtered['feat']= $filtered['feat']==='featured' ? 'featured' : '';
  foreach($filtered as $k=>$v) if(is_string($v)) $filtered[$k]=mb_substr(trim($v),0,5000);
  $filtered['id']=genId();
  $cols=array_keys($filtered); $vals=array_values($filtered);
  $ph=implode(',',array_fill(0,count($cols),'?'));
  $pdo->prepare("INSERT INTO $table (".implode(',',$cols).") VALUES ($ph)")->execute($vals);
  json(['ok'=>true,'id'=>$filtered['id']]);
}

// PUT update
if($method==='PUT' || $method==='PATCH'){
  $b=body(); $id=$b['id']??$_GET['id']??'';
  if(!$id) json(['error'=>'id required'],400);
  $filtered=filterBody($b,$colsAllowed);
  if(!$filtered) json(['error'=>'Nothing to update'],400);
  if(isset($filtered['kat']) && !in_array($filtered['kat'],['akademik','seni','olahraga'])) json(['error'=>'Kategori tidak valid'],422);
  if(isset($filtered['color']) && !preg_match('/^#[0-9a-fA-F]{6}$/',$filtered['color'])) $filtered['color']='#0F5132';
  if(isset($filtered['icon'])) $filtered['icon']=preg_replace('/[^a-z0-9-]/','', $filtered['icon']);
  if(isset($filtered['wide'])) $filtered['wide']= $filtered['wide']==='wide' ? 'wide' : '';
  if(isset($filtered['feat'])) $filtered['feat']= $filtered['feat']==='featured' ? 'featured' : '';
  $sets=[]; $vals=[];
  foreach($filtered as $k=>$v){
    $sets[]="$k=?";
    $vals[]=is_string($v)?mb_substr(trim($v),0,5000):$v;
  }
  $vals[]=$id;
  $pdo->prepare("UPDATE $table SET ".implode(',',$sets)." WHERE id=?")->execute($vals);
  json(['ok'=>true]);
}

// DELETE
if($method==='DELETE'){
  $id=$_GET['id']??body()['id']??'';
  if(!$id) json(['error'=>'id required'],400);
  $pdo->prepare("DELETE FROM $table WHERE id=?")->execute([$id]);
  json(['ok'=>true]);
}
json(['error'=>'Method not allowed'],405);
