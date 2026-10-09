<?php
require __DIR__.'/config.php';
requireAuth(['admin','editor']);
header('Content-Type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] !== 'POST') json(['error'=>'Method not allowed'],405);
if(empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) json(['error'=>'File tidak ditemukan'],400);

$f = $_FILES['file'];
if($f['size'] > 5*1024*1024) json(['error'=>'Maks 5MB'],413);
if($f['size'] === 0) json(['error'=>'File kosong'],400);

// validate is image
$info = @getimagesize($f['tmp_name']);
if(!$info) json(['error'=>'File bukan gambar'],400);
$mime = $info['mime'] ?? '';
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
if(!isset($allowed[$mime])) json(['error'=>'Format harus JPG/PNG/WEBP/GIF'],400);
$ext = $allowed[$mime];

// extra MIME check via finfo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime2 = finfo_file($finfo, $f['tmp_name']);
finfo_close($finfo);
if(!isset($allowed[$mime2])) json(['error'=>'Format tidak diizinkan'],400);

$dir = __DIR__ . '/../uploads';
if(!is_dir($dir)) mkdir($dir, 0755, true);
if(!is_writable($dir)) json(['error'=>'Folder uploads tidak writable'],500);

$name = 'mdu_' . bin2hex(random_bytes(8)) . '.' . $ext;
$dest = $dir . '/' . $name;
if(!move_uploaded_file($f['tmp_name'], $dest)) json(['error'=>'Gagal simpan file'],500);

// url relative to site root
$url = 'uploads/' . $name;
json(['ok'=>true,'url'=>$url,'mime'=>$mime]);
