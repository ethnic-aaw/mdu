<?php
require __DIR__.'/config.php';
$method=$_SERVER['REQUEST_METHOD'];
$input=body();

if($method==='GET'){
  if(($_GET['action']??'')==='me'){
    if(empty($_SESSION['user'])) json(['user'=>null],401);
    json(['user'=>$_SESSION['user']]);
  }
  json(['ok'=>true]);
}

if($method==='POST'){
  $action=$input['action']??$_GET['action']??'login';

  if($action==='logout'){
    $_SESSION=[]; if(ini_get("session.use_cookies")){ $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p["path"],$p["domain"]??'', $p["secure"], $p["httponly"]); }
    session_destroy();
    json(['ok'=>true]);
  }

  if($action==='login'){
    $ip=$_SERVER['REMOTE_ADDR']??'unknown';
    throttle("login:$ip", 8, 60); // 8 attempts/min
    $u=trim($input['username']??''); $p=$input['password']??'';
    if($u===''||$p==='') json(['error'=>'Username & password wajib'],400);
    // bounded length
    if(strlen($u)>60 || strlen($p)>128) json(['error'=>'Input terlalu panjang'],400);
    $pdo=getPDO();
    $st=$pdo->prepare("SELECT * FROM users WHERE username=?");
    $st->execute([$u]); $row=$st->fetch(PDO::FETCH_ASSOC);
    if(!$row){
      // constant-time dummy hash to hinder enumeration
      password_verify($p, '$2y$10$aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
      json(['error'=>'Username/password salah'],401);
    }
    $ok=password_verify($p,$row['pass']) || $row['pass']===base64_encode($p) || hash('sha256',$p)===$row['pass'];
    if($ok && !password_verify($p,$row['pass'])){
      $new=password_hash($p,PASSWORD_DEFAULT);
      $pdo->prepare("UPDATE users SET pass=? WHERE id=?")->execute([$new,$row['id']]);
    }
    if(!$ok) json(['error'=>'Username/password salah'],401);
    session_regenerate_id(true);
    $_SESSION['user']=['id'=>$row['id'],'username'=>$row['username'],'nama'=>$row['nama'],'role'=>$row['role']];
    json(['ok'=>true,'user'=>$_SESSION['user']]);
  }
}
json(['error'=>'Not found'],404);
