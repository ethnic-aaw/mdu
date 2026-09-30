<?php
// Hardened session before start
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
  'lifetime'=>0, 'path'=>'/', 'httponly'=>true, 'secure'=>$secure, 'samesite'=>'Lax'
]);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

define('DB_PATH', __DIR__ . '/../data/mdu.db');

function getPDO(){
  static $pdo=null;
  if($pdo) return $pdo;
  $dir=dirname(DB_PATH);
  if(!is_dir($dir)) mkdir($dir,0755,true);
  // ensure writable on Docker Linux volumes; auto-repair perms without crash
  if(is_dir($dir) && !is_writable($dir)) @chmod($dir,0775);
  if(is_file(DB_PATH) && !is_writable(DB_PATH)) @chmod(DB_PATH,0664);
  try{ $pdo=new PDO('sqlite:'.DB_PATH); }catch(PDOException $e){
    // retry after chmod — surface clear error if still fails
    @chmod($dir,0775); @chmod(DB_PATH,0664);
    throw $e;
  }
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // WAL for concurrency
  try{ $pdo->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON;'); }catch(Exception $e){}
  return $pdo;
}
function json($data,$code=200){
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function body(){
  $raw=file_get_contents('php://input');
  $j=json_decode($raw,true);
  return is_array($j)?$j:[];
}
function requireAuth($roles=null){
  if(empty($_SESSION['user'])) json(['error'=>'Unauthorized'],401);
  if($roles && !in_array($_SESSION['user']['role'], (array)$roles)) json(['error'=>'Forbidden'],403);
}
function genId(){ return 'id'.bin2hex(random_bytes(4)).substr(uniqid(),-3); }
function esc_html($s){ return htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
// allow only span/br/em in hero titles
function sanitizeHtmlTitle($html){
  $html = preg_replace('#<script.*?>.*?</script>#is','',$html);
  $allowed = '<span><br><em><b><strong><i>';
  return strip_tags($html, $allowed);
}
// simple file throttle: max $limit hits per $window seconds per IP+action
function throttle($key,$limit=5,$window=60){
  $dir=dirname(DB_PATH); $f="$dir/.throttle_".md5($key).".json";
  $now=time(); $data=[]; if(is_file($f)) $data=json_decode(@file_get_contents($f),true)?:[];
  $data=array_values(array_filter($data, fn($t)=> $t > $now - $window));
  if(count($data) >= $limit) json(['error'=>'Terlalu banyak permintaan, coba lagi nanti.'],429);
  $data[]=$now; @file_put_contents($f, json_encode($data));
}

function initDB(){
  $pdo=getPDO();
  $pdo->exec("CREATE TABLE IF NOT EXISTS users (id TEXT PRIMARY KEY, username TEXT UNIQUE NOT NULL, nama TEXT, pass TEXT NOT NULL, role TEXT NOT NULL)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS site (id INTEGER PRIMARY KEY CHECK(id=1), data TEXT NOT NULL)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS berita (id TEXT PRIMARY KEY, judul TEXT, excerpt TEXT, konten TEXT, img TEXT, tgl TEXT, tag TEXT, tagClass TEXT)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS program (id TEXT PRIMARY KEY, icon TEXT, judul TEXT, desc TEXT, feat TEXT)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fasilitas (id TEXT PRIMARY KEY, judul TEXT, desc TEXT, img TEXT, icon TEXT, wide TEXT)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS ekskul (id TEXT PRIMARY KEY, judul TEXT, desc TEXT, icon TEXT, color TEXT)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS prestasi (id TEXT PRIMARY KEY, judul TEXT, level TEXT, lokasi TEXT, kat TEXT, img TEXT)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS testimoni (id TEXT PRIMARY KEY, nama TEXT, peran TEXT, foto TEXT, teks TEXT)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS asatidz (id TEXT PRIMARY KEY, nama TEXT, jabatan TEXT, foto TEXT, sambutan TEXT, urut INTEGER DEFAULT 0)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS inbox (id TEXT PRIMARY KEY, nama TEXT, kontak TEXT, subjek TEXT, pesan TEXT, tgl TEXT, is_read INTEGER DEFAULT 0)");
  // migrate asatidz.urut if missing (existing DB)
  try{
    $cols=$pdo->query("PRAGMA table_info(asatidz)")->fetchAll(PDO::FETCH_ASSOC);
    $hasUrut=false; foreach($cols as $c){ if($c['name']==='urut') $hasUrut=true; }
    if(!$hasUrut) $pdo->exec("ALTER TABLE asatidz ADD COLUMN urut INTEGER DEFAULT 0");
  }catch(Exception $e){}
  // migrate berita.konten if missing (existing DB)
  try{
    $cols=$pdo->query("PRAGMA table_info(berita)")->fetchAll(PDO::FETCH_ASSOC);
    $hasKonten=false; foreach($cols as $c){ if($c['name']==='konten') $hasKonten=true; }
    if(!$hasKonten) $pdo->exec("ALTER TABLE berita ADD COLUMN konten TEXT DEFAULT ''");
  }catch(Exception $e){}
  // migrate legacy `read` column -> is_read if exists
  try{
    $cols=$pdo->query("PRAGMA table_info(inbox)")->fetchAll(PDO::FETCH_ASSOC);
    $hasRead=false; $hasIsRead=false;
    foreach($cols as $c){ if($c['name']==='read') $hasRead=true; if($c['name']==='is_read') $hasIsRead=true; }
    if($hasRead && !$hasIsRead){
      $pdo->exec("ALTER TABLE inbox ADD COLUMN is_read INTEGER DEFAULT 0");
      $pdo->exec("UPDATE inbox SET is_read = \"read\"");
    }
    if($hasRead && $hasIsRead){
      // keep is_read, drop read not supported in sqlite easily — leave both
    }
  }catch(Exception $e){}

  $c=$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
  if($c==0){
    $id='u1'; $pass=password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users(id,username,nama,pass,role) VALUES(?,?,?,?,?)")->execute([$id,'admin','Super Admin',$pass,'admin']);
  }
  $c=$pdo->query("SELECT COUNT(*) FROM site")->fetchColumn();
  if($c==0){
    $site=[
      'heroTitle'=>'MDU <span>AL-ITTIHAD</span><br>Sumberjaya Majalengka',
      'slogan'=>'“Unggul dalam Prestasi, Berkarakter dalam Budaya”',
      'heroDesc'=>'Lembaga pendidikan diniyah tingkat dasar yang memadukan ilmu agama, karakter islami dan prestasi — mencetak generasi Qur\'ani yang modern.',
      'stats'=>[['v'=>'500+','l'=>'Santri Aktif'],['v'=>'25+','l'=>'Tenaga Pendidik'],['v'=>'30+','l'=>'Prestasi'],['v'=>'15','l'=>'Tahun Mengabdi']],
      'tentangTitle'=>'Mencetak Generasi Qur\'ani yang <em>Unggul & Berkarakter</em>',
      'tentangDesc'=>'MDU Al-Ittihad Sumberjaya Majalengka berdiri sebagai pelengkap pendidikan formal, membekali santri dengan fondasi keislaman yang kokoh sejak usia dini.',
      'visi'=>'Terwujudnya madrasah diniyah yang unggul dalam prestasi, berkarakter islami, dan berwawasan kebangsaan.',
      'misi'=>['Menyelenggarakan pendidikan Al-Qur\'an, Fiqih, Akidah Akhlak & Bahasa Arab yang komprehensif.','Membina karakter santri berakhlakul karimah dan cinta budaya lokal.','Mengembangkan bakat santri melalui ekstrakurikuler dan pembinaan prestasi.','Membangun kemitraan erat dengan orang tua, masyarakat & instansi.'],
      'sambutan'=>['nama'=>'Ust. H. Ahmad Fauzi, S.Pd.I','jabatan'=>'Kepala MDU Al-Ittihad','foto'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80','judul'=>"Assalamu'alaikum Warahmatullahi Wabarakatuh",'p1'=>"Puji syukur ke hadirat Allah SWT. MDU Al-Ittihad hadir sebagai rumah kedua bagi putra-putri kita — tempat mereka mencintai Al-Qur'an, memahami agama dengan benar, dan tumbuh berkarakter.",'p2'=>"Kami percaya pendidikan diniyah bukan sekadar tambahan, melainkan fondasi. Di era digital ini, kami berkomitmen menjaga tradisi keilmuan pesantren sambil membuka diri pada inovasi pembelajaran.",'p3'=>"Kami mengundang Ayah/Bunda untuk bergabung, melihat langsung suasana belajar, dan menjadi bagian dari keluarga besar Al-Ittihad."],
      'heroSlides'=>['https://images.unsplash.com/photo-1562774053-701939374585?w=1600&q=80','https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1600&q=80','https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600&q=80'],'kontak'=>['alamat'=>'Bongas Wetan, Kec. Sumberjaya, Kabupaten Majalengka, Jawa Barat 45455','tel'=>'0812-3456-7890','email'=>'info@mdu-alittihad.sch.id']
    ];
    $pdo->prepare("INSERT INTO site(id,data) VALUES(1,?)")->execute([json_encode($site,JSON_UNESCAPED_UNICODE)]);
  }
  $seeds=[
    'berita'=>[
      ['id'=>'b1','judul'=>'Haflah Akhirussanah & Wisuda Tahfidz Angkatan XII','excerpt'=>'Sebanyak 42 santri diwisuda tahfidz juz 30 dengan penampilan marawis dan kaligrafi memukau.','img'=>'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80','tgl'=>'2026-09-20','tag'=>'Kegiatan','tagClass'=>''],
      ['id'=>'b2','judul'=>'Santri Raih Juara 1 MTQ Tingkat Kabupaten Majalengka','excerpt'=>'Ananda Fatimah Az-Zahra harumkan nama madrasah di ajang MTQ cabang Tilawah Anak.','img'=>'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600&q=80','tgl'=>'2026-09-12','tag'=>'Prestasi','tagClass'=>'gold'],
      ['id'=>'b3','judul'=>'Pendaftaran Santri Baru 2026/2027 Telah Dibuka','excerpt'=>'Kuota terbatas. Dapatkan potongan biaya pendaftaran untuk 30 pendaftar pertama.','img'=>'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&q=80','tgl'=>'2026-09-05','tag'=>'Pengumuman','tagClass'=>'green'],
    ],
    'program'=>[
      ['id'=>'p1','icon'=>'fa-book-quran','judul'=>"Al-Qur'an & Tahfidz",'desc'=>'Tahsin, tahfidz juz 30, dan tafsir tematik dengan metode menyenangkan.','feat'=>''],
      ['id'=>'p2','icon'=>'fa-scale-balanced','judul'=>'Fiqih Ibadah','desc'=>'Praktik wudhu, sholat, puasa & muamalah sesuai madzhab Syafi\'i.','feat'=>''],
      ['id'=>'p3','icon'=>'fa-heart','judul'=>'Akidah Akhlak','desc'=>'Pembinaan karakter, adab sehari-hari & kisah teladan sahabat.','feat'=>''],
      ['id'=>'p4','icon'=>'fa-language','judul'=>'Bahasa Arab','desc'=>'Muhadatsah, mufrodat & nahwu dasar untuk akses kitab kuning.','feat'=>''],
      ['id'=>'p5','icon'=>'fa-scroll','judul'=>'Sejarah Kebudayaan Islam','desc'=>'Jejak peradaban Islam dari Rasulullah hingga kejayaan nusantara.','feat'=>''],
      ['id'=>'p6','icon'=>'fa-pen-nib','judul'=>'Kaligrafi & Seni Islami','desc'=>'Latihan khat, dekorasi islami & kreativitas santri.','feat'=>''],
      ['id'=>'p7','icon'=>'fa-people-group','judul'=>'Pembinaan Karakter','desc'=>'Disiplin, kepemimpinan & kepedulian sosial berbasis pesantren.','feat'=>''],
      ['id'=>'p8','icon'=>'fa-laptop','judul'=>'Literasi Digital','desc'=>'Pengenalan teknologi yang bijak & aman untuk santri milenial.','feat'=>'featured'],
    ],
    'fasilitas'=>[
      ['id'=>'f1','judul'=>'Laboratorium','desc'=>'Ruang praktik sains sederhana & komputer edukasi.','img'=>'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80','icon'=>'fa-flask','wide'=>''],
      ['id'=>'f2','judul'=>'Perpustakaan','desc'=>'Koleksi kitab, buku umum & pojok literasi santri.','img'=>'https://images.unsplash.com/photo-1526243741027-d5585c4e06da?w=600&q=80','icon'=>'fa-book-open','wide'=>''],
      ['id'=>'f3','judul'=>'Masjid','desc'=>'Pusat ibadah, tahfidz & pembinaan akhlak harian.','img'=>'https://images.unsplash.com/photo-1590075865002-3a6b0b0df3f0?w=600&q=80','icon'=>'fa-mosque','wide'=>''],
      ['id'=>'f4','judul'=>'Aula','desc'=>'Acara haflah, seminar & pertemuan wali santri.','img'=>'https://images.unsplash.com/photo-1511578314322-379afb476865?w=600&q=80','icon'=>'fa-landmark','wide'=>''],
      ['id'=>'f5','judul'=>'Ruang Multimedia','desc'=>'Proyektor, audio & media pembelajaran interaktif.','img'=>'https://images.unsplash.com/photo-1496171367470-9ed9a570523a?w=900&q=80','icon'=>'fa-photo-film','wide'=>'wide'],
    ],
    'ekskul'=>[
      ['id'=>'e1','judul'=>'Marawis / Hadroh','desc'=>'Seni rebana & sholawat, tampil di haflah & lomba.','icon'=>'fa-drum','color'=>'#0F5132'],
      ['id'=>'e2','judul'=>'Kaligrafi','desc'=>'Seni khat Arab gaya Naskhi & Diwani.','icon'=>'fa-paintbrush','color'=>'#1B7A43'],
      ['id'=>'e3','judul'=>'Tahfidz Club','desc'=>'Halaqah intensif juz 30 & 29, setoran harian.','icon'=>'fa-book-quran','color'=>'#C5A253'],
      ['id'=>'e4','judul'=>'Pramuka','desc'=>'Kemandirian, kedisiplinan & cinta alam.','icon'=>'fa-person-hiking','color'=>'#2E7D32'],
      ['id'=>'e5','judul'=>'Muhadhoroh','desc'=>'Public speaking dakwah 3 bahasa.','icon'=>'fa-microphone','color'=>'#6D4C41'],
      ['id'=>'e6','judul'=>'Olahraga','desc'=>'Futsal, badminton & panahan sunnah.','icon'=>'fa-futbol','color'=>'#1565C0'],
    ],
    'prestasi'=>[
      ['id'=>'pr1','judul'=>'Juara 1 MTQ Tilawah Anak','level'=>'Kabupaten • 2026','lokasi'=>'Kab. Majalengka','kat'=>'akademik','img'=>'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=600&q=80'],
      ['id'=>'pr2','judul'=>'Juara 1 Kaligrafi','level'=>'Kecamatan • 2025','lokasi'=>'Kec. Sumberjaya','kat'=>'seni','img'=>'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=600&q=80'],
      ['id'=>'pr3','judul'=>'Juara 2 Futsal Antar MDU','level'=>'Kabupaten • 2025','lokasi'=>'Kab. Majalengka','kat'=>'olahraga','img'=>'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=600&q=80'],
      ['id'=>'pr4','judul'=>'Juara Harapan Marawis','level'=>'Provinsi • 2024','lokasi'=>'Jawa Barat','kat'=>'seni','img'=>'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600&q=80'],
      ['id'=>'pr5','judul'=>'Juara 1 Cerdas Cermat Islami','level'=>'Kecamatan • 2024','lokasi'=>'Kec. Sumberjaya','kat'=>'akademik','img'=>'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80'],
      ['id'=>'pr6','judul'=>'Juara 3 Panahan Tradisional','level'=>'Kabupaten • 2023','lokasi'=>'Kab. Majalengka','kat'=>'olahraga','img'=>'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&q=80'],
    ],
    'testimoni'=>[
      ['id'=>'t1','nama'=>'Ibu Siti Aminah','peran'=>'Wali Santri • 2024','foto'=>'https://i.pravatar.cc/120?img=5','teks'=>'Alhamdulillah anak saya hafal juz 30 dalam 1 tahun. Gurunya sabar dan metode tahfidznya menyenangkan.'],
      ['id'=>'t2','nama'=>'Rizki Maulana','peran'=>'Alumni 2023 • MTsN 1 Majalengka','foto'=>'https://i.pravatar.cc/120?img=8','teks'=>'Di MDU saya belajar marawis dan kaligrafi. Sekarang berani tampil di depan umum. Terima kasih ustadz/ustadzah!'],
      ['id'=>'t3','nama'=>'Bpk. Dedi Hermawan','peran'=>'Wali Santri • 2025','foto'=>'https://i.pravatar.cc/120?img=9','teks'=>'Lingkungannya islami, fasilitas lengkap. Anak jadi lebih disiplin sholat dan cinta Al-Qur\'an.'],
    ],
    'asatidz'=>[
      ['id'=>'a1','nama'=>'Ust. Ahmad Fauzi, S.Pd.I','jabatan'=>'Kepala MDU','foto'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80','sambutan'=>'Assalamu\'alaikum, mari bersama mencetak generasi Qur\'ani yang berkarakter.','urut'=>1],
      ['id'=>'a2','nama'=>'Ust. Syarif Hidayat','jabatan'=>'Wakil Kepala','foto'=>'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80','sambutan'=>'Pendidikan diniyah fondasi akhlak mulia.','urut'=>2],
      ['id'=>'a3','nama'=>'Ust. Fatimah Zahra','jabatan'=>'Guru Tahfidz','foto'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&q=80','sambutan'=>'Tahfidz menyenangkan untuk santri sejak dini.','urut'=>3],
      ['id'=>'a4','nama'=>'Ust. Ridwan Kamil','jabatan'=>'Guru Fiqih','foto'=>'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&q=80','sambutan'=>'Fiqih praktis membekali ibadah sehari-hari.','urut'=>4],
    ],
  ];
  foreach($seeds as $table=>$rows){
    if($pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn()==0){
      foreach($rows as $r){
        $cols=array_keys($r); $ph=implode(',',array_fill(0,count($cols),'?'));
        $pdo->prepare("INSERT INTO $table (".implode(',',$cols).") VALUES ($ph)")->execute(array_values($r));
      }
    }
  }
  // migrate alamat lama → Bongas Wetan
  try{
    $raw=$pdo->query("SELECT data FROM site WHERE id=1")->fetchColumn();
    $sd=json_decode($raw,true);
    $old='Jl. Pesantren No. 12'; $newAddr='Bongas Wetan, Kec. Sumberjaya, Kabupaten Majalengka, Jawa Barat 45455';
    if(isset($sd['kontak']['alamat']) && str_contains($sd['kontak']['alamat'],$old)){
      $sd['kontak']['alamat']=$newAddr;
      $pdo->prepare("UPDATE site SET data=? WHERE id=1")->execute([json_encode($sd,JSON_UNESCAPED_UNICODE)]);
    }
  }catch(Exception $e){}
  try{
    $raw2=$pdo->query("SELECT data FROM site WHERE id=1")->fetchColumn();
    $sd2=json_decode($raw2,true);
    if(!isset($sd2['heroSlides']) || !is_array($sd2['heroSlides'])){
      $sd2['heroSlides']=['https://images.unsplash.com/photo-1562774053-701939374585?w=1600&q=80','https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1600&q=80'];
      $pdo->prepare("UPDATE site SET data=? WHERE id=1")->execute([json_encode($sd2,JSON_UNESCAPED_UNICODE)]);
    }
  }catch(Exception $e){}
}
initDB();
