<?php
require __DIR__.'/config.php';
$pdo=getPDO();
$site=json_decode($pdo->query("SELECT data FROM site WHERE id=1")->fetchColumn(), true);
function fetchAll($table){ $pdo=getPDO(); return $pdo->query("SELECT * FROM $table ORDER BY rowid DESC")->fetchAll(PDO::FETCH_ASSOC); }
$data=[
  'site'=>$site,
  'berita'=>fetchAll('berita'),
  'program'=>fetchAll('program'),
  'fasilitas'=>fetchAll('fasilitas'),
  'ekskul'=>fetchAll('ekskul'),
  'prestasi'=>fetchAll('prestasi'),
  'testimoni'=>fetchAll('testimoni'),
];
header('Cache-Control: no-store, must-revalidate');
json($data);
