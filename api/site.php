<?php
require __DIR__.'/config.php';
$pdo=getPDO();
if($_SERVER['REQUEST_METHOD']==='GET'){
  $data=json_decode($pdo->query("SELECT data FROM site WHERE id=1")->fetchColumn(), true);
  json($data);
}
if($_SERVER['REQUEST_METHOD']=='POST' || $_SERVER['REQUEST_METHOD']=='PUT'){
  requireAuth(['admin','editor']);
  $b=body();
  $allow=['heroTitle','slogan','heroDesc','stats','tentangTitle','tentangDesc','visi','misi','sambutan','sambutanAmm','kontak','heroSlides'];
  $cur=json_decode($pdo->query("SELECT data FROM site WHERE id=1")->fetchColumn(), true);
  foreach($allow as $k){ if(array_key_exists($k,$b)) $cur[$k]=$b[$k]; }
  if(isset($cur['heroTitle'])) $cur['heroTitle']=sanitizeHtmlTitle($cur['heroTitle']);
  foreach(['heroDesc','tentangDesc','visi'] as $k){ if(isset($cur[$k]) && is_string($cur[$k])) $cur[$k]=mb_substr($cur[$k],0,5000); }
  foreach(['slogan','tentangTitle'] as $k){ if(isset($cur[$k]) && is_string($cur[$k])) $cur[$k]=mb_substr(strip_tags($cur[$k]),0,300); }
  if(isset($cur['misi']) && !is_array($cur['misi'])) $cur['misi']=[];
  else $cur['misi']=array_values(array_filter(array_map(fn($s)=>mb_substr(trim($s),0,300), (array)$cur['misi'])));
  if(isset($cur['stats']) && is_array($cur['stats'])){
    $cur['stats']=array_slice(array_map(fn($s)=>['v'=>mb_substr(trim($s['v']??''),0,20),'l'=>mb_substr(trim($s['l']??''),0,40)], $cur['stats']),0,6);
  } else $cur['stats']=[];
  if(isset($cur['heroSlides']) && is_array($cur['heroSlides'])){
    $cur['heroSlides']=array_values(array_filter(array_map(function($u){
      $u=trim((string)$u); if($u==='') return '';
      if(preg_match('#^https?://#',$u) || str_starts_with($u,'uploads/') || str_starts_with($u,'data:')) return mb_substr($u,0,2000);
      return '';
    }, $cur['heroSlides'])));
    $cur['heroSlides']=array_slice($cur['heroSlides'],0,8);
  } else $cur['heroSlides']=[];
  foreach(['sambutan','sambutanAmm'] as $sk){
    if(isset($cur[$sk]) && is_array($cur[$sk])){
      foreach(['nama','jabatan','foto','judul','p1','p2','p3'] as $k){ if(isset($cur[$sk][$k]) && is_string($cur[$sk][$k])) $cur[$sk][$k]=mb_substr(trim($cur[$sk][$k]),0,2000); }
      if(!empty($cur[$sk]['foto']) && !filter_var($cur[$sk]['foto'],FILTER_VALIDATE_URL) && !str_starts_with($cur[$sk]['foto'],'uploads/')) $cur[$sk]['foto']='';
    }
  }
  if(isset($cur['kontak']) && is_array($cur['kontak'])){
    foreach(['alamat','tel','email'] as $k){ if(isset($cur['kontak'][$k]) && is_string($cur['kontak'][$k])) $cur['kontak'][$k]=mb_substr(trim($cur['kontak'][$k]),0,500); }
    if(isset($cur['kontak']['email']) && $cur['kontak']['email']!=='' && !filter_var($cur['kontak']['email'],FILTER_VALIDATE_EMAIL)) json(['error'=>'Email tidak valid'],422);
    if(isset($cur['kontak']['tel']) && $cur['kontak']['tel']!=='' && !preg_match('/^[0-9+ \-\(\)]{8,20}$/',$cur['kontak']['tel'])) json(['error'=>'No. tel tidak valid'],422);
  }
  $pdo->prepare("UPDATE site SET data=? WHERE id=1")->execute([json_encode($cur,JSON_UNESCAPED_UNICODE)]);
  json(['ok'=>true,'data'=>$cur]);
}
json(['error'=>'Method not allowed'],405);
