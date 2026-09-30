<?php
require __DIR__.'/config.php';
$pdo=getPDO();
$site=json_decode($pdo->query("SELECT data FROM site WHERE id=1")->fetchColumn(), true);
function fetchAll($table){ $pdo=getPDO(); $ord=$table==='asatidz'?"COALESCE(urut,9999) ASC, rowid DESC":"rowid DESC"; return $pdo->query("SELECT * FROM $table ORDER BY $ord")->fetchAll(PDO::FETCH_ASSOC); }
$data=[
  'site'=>$site,
  'berita'=>fetchAll('berita'),
  'program'=>fetchAll('program'),
  'fasilitas'=>fetchAll('fasilitas'),
  'ekskul'=>fetchAll('ekskul'),
  'prestasi'=>fetchAll('prestasi'),
  'testimoni'=>fetchAll('testimoni'),
  'asatidz'=>fetchAll('asatidz'),
];
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
json($data);
