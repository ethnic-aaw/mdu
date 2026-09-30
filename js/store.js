// Store API-backed (SQLite) + localStorage fallback (offline/file://)
const Store={
  defaults:{
    site:{heroTitle:'MDU <span>AL-ITTIHAD</span><br>Sumberjaya Majalengka',slogan:'“Unggul dalam Prestasi, Berkarakter dalam Budaya”',heroDesc:'Lembaga pendidikan diniyah tingkat dasar yang memadukan ilmu agama, karakter islami dan prestasi — mencetak generasi Qur\'ani yang modern.',stats:[{v:'500+',l:'Santri Aktif'},{v:'25+',l:'Tenaga Pendidik'},{v:'30+',l:'Prestasi'},{v:'15',l:'Tahun Mengabdi'}],tentangTitle:'Mencetak Generasi Qur\'ani yang <em>Unggul & Berkarakter</em>',tentangDesc:'MDU Al-Ittihad Sumberjaya Majalengka berdiri sebagai pelengkap pendidikan formal, membekali santri dengan fondasi keislaman yang kokoh sejak usia dini.',visi:'Terwujudnya madrasah diniyah yang unggul dalam prestasi, berkarakter islami, dan berwawasan kebangsaan.',misi:['Menyelenggarakan pendidikan Al-Qur\'an, Fiqih, Akidah Akhlak & Bahasa Arab yang komprehensif.','Membina karakter santri berakhlakul karimah dan cinta budaya lokal.','Mengembangkan bakat santri melalui ekstrakurikuler dan pembinaan prestasi.','Membangun kemitraan erat dengan orang tua, masyarakat & instansi.'],sambutan:{nama:'Ust. H. Ahmad Fauzi, S.Pd.I',jabatan:'Kepala MDU Al-Ittihad',foto:'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80',judul:"Assalamu'alaikum Warahmatullahi Wabarakatuh",p1:"Puji syukur ke hadirat Allah SWT. MDU Al-Ittihad hadir sebagai rumah kedua bagi putra-putri kita — tempat mereka mencintai Al-Qur'an, memahami agama dengan benar, dan tumbuh berkarakter.",p2:"Kami percaya pendidikan diniyah bukan sekadar tambahan, melainkan fondasi. Di era digital ini, kami berkomitmen menjaga tradisi keilmuan pesantren sambil membuka diri pada inovasi pembelajaran.",p3:"Kami mengundang Ayah/Bunda untuk bergabung, melihat langsung suasana belajar, dan menjadi bagian dari keluarga besar Al-Ittihad."},sambutanAmm:{nama:'KH. Ahmad Syarif',jabatan:'Mudirul Am / Pengasuh Pondok Pesantren',foto:'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&q=80',judul:"Assalamu'alaikum Warahmatullahi Wabarakatuh",p1:"Puji syukur ke hadirat Allah SWT. Pesantren kami berdiri sebagai pusat tarbiyah, dakwah, dan khidmah — membina santri berakhlakul karimah, cinta ilmu, dan siap mengabdi untuk umat.",p2:"MDU Al-Ittihad adalah bagian dari keluarga besar pesantren. Semoga menjadi wasilah lahirnya generasi Qur'ani yang unggul, berkarakter, dan bermanfaat bagi agama, nusa, dan bangsa.",p3:"Mari bersama menjaga tradisi pesantren sambil menyongsong masa depan dengan ilmu dan adab."},heroSlides:['https://images.unsplash.com/photo-1562774053-701939374585?w=1600&q=80','https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1600&q=80','https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600&q=80'],kontak:{alamat:'Bongas Wetan, Kec. Sumberjaya, Kabupaten Majalengka, Jawa Barat 45455',tel:'0812-3456-7890',email:'info@mdu-alittihad.sch.id'}},
    berita:[
      {id:'b1',judul:'Haflah Akhirussanah & Wisuda Tahfidz Angkatan XII',excerpt:'Sebanyak 42 santri diwisuda tahfidz juz 30 dengan penampilan marawis dan kaligrafi memukau.',img:'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80',tgl:'2026-09-20',tag:'Kegiatan',tagClass:''},
      {id:'b2',judul:'Santri Raih Juara 1 MTQ Tingkat Kabupaten Majalengka',excerpt:'Ananda Fatimah Az-Zahra harumkan nama madrasah di ajang MTQ cabang Tilawah Anak.',img:'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600&q=80',tgl:'2026-09-12',tag:'Prestasi',tagClass:'gold'},
      {id:'b3',judul:'Pendaftaran Santri Baru 2026/2027 Telah Dibuka',excerpt:'Kuota terbatas. Dapatkan potongan biaya pendaftaran untuk 30 pendaftar pertama.',img:'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&q=80',tgl:'2026-09-05',tag:'Pengumuman',tagClass:'green'}
    ],
    program:[
      {id:'p1',icon:'fa-book-quran',judul:'Al-Qur\'an & Tahfidz',desc:'Tahsin, tahfidz juz 30, dan tafsir tematik dengan metode menyenangkan.',feat:''},
      {id:'p2',icon:'fa-scale-balanced',judul:'Fiqih Ibadah',desc:'Praktik wudhu, sholat, puasa & muamalah sesuai madzhab Syafi\'i.',feat:''},
      {id:'p3',icon:'fa-heart',judul:'Akidah Akhlak',desc:'Pembinaan karakter, adab sehari-hari & kisah teladan sahabat.',feat:''},
      {id:'p4',icon:'fa-language',judul:'Bahasa Arab',desc:'Muhadatsah, mufrodat & nahwu dasar untuk akses kitab kuning.',feat:''},
      {id:'p5',icon:'fa-scroll',judul:'Sejarah Kebudayaan Islam',desc:'Jejak peradaban Islam dari Rasulullah hingga kejayaan nusantara.',feat:''},
      {id:'p6',icon:'fa-pen-nib',judul:'Kaligrafi & Seni Islami',desc:'Latihan khat, dekorasi islami & kreativitas santri.',feat:''},
      {id:'p7',icon:'fa-people-group',judul:'Pembinaan Karakter',desc:'Disiplin, kepemimpinan & kepedulian sosial berbasis pesantren.',feat:''},
      {id:'p8',icon:'fa-laptop',judul:'Literasi Digital',desc:'Pengenalan teknologi yang bijak & aman untuk santri milenial.',feat:'featured'}
    ],
    fasilitas:[
      {id:'f1',judul:'Laboratorium',desc:'Ruang praktik sains sederhana & komputer edukasi.',img:'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80',icon:'fa-flask',wide:''},
      {id:'f2',judul:'Perpustakaan',desc:'Koleksi kitab, buku umum & pojok literasi santri.',img:'https://images.unsplash.com/photo-1526243741027-d5585c4e06da?w=600&q=80',icon:'fa-book-open',wide:''},
      {id:'f3',judul:'Masjid',desc:'Pusat ibadah, tahfidz & pembinaan akhlak harian.',img:'https://images.unsplash.com/photo-1590075865002-3a6b0b0df3f0?w=600&q=80',icon:'fa-mosque',wide:''},
      {id:'f4',judul:'Aula',desc:'Acara haflah, seminar & pertemuan wali santri.',img:'https://images.unsplash.com/photo-1511578314322-379afb476865?w=600&q=80',icon:'fa-landmark',wide:''},
      {id:'f5',judul:'Ruang Multimedia',desc:'Proyektor, audio & media pembelajaran interaktif.',img:'https://images.unsplash.com/photo-1496171367470-9ed9a570523a?w=900&q=80',icon:'fa-photo-film',wide:'wide'}
    ],
    ekskul:[
      {id:'e1',judul:'Marawis / Hadroh',desc:'Seni rebana & sholawat, tampil di haflah & lomba.',icon:'fa-drum',color:'#0F5132'},
      {id:'e2',judul:'Kaligrafi',desc:'Seni khat Arab gaya Naskhi & Diwani.',icon:'fa-paintbrush',color:'#1B7A43'},
      {id:'e3',judul:'Tahfidz Club',desc:'Halaqah intensif juz 30 & 29, setoran harian.',icon:'fa-book-quran',color:'#C5A253'},
      {id:'e4',judul:'Pramuka',desc:'Kemandirian, kedisiplinan & cinta alam.',icon:'fa-person-hiking',color:'#2E7D32'},
      {id:'e5',judul:'Muhadhoroh',desc:'Public speaking dakwah 3 bahasa.',icon:'fa-microphone',color:'#6D4C41'},
      {id:'e6',judul:'Olahraga',desc:'Futsal, badminton & panahan sunnah.',icon:'fa-futbol',color:'#1565C0'}
    ],
    prestasi:[
      {id:'pr1',judul:'Juara 1 MTQ Tilawah Anak',level:'Kabupaten • 2026',lokasi:'Kab. Majalengka',kat:'akademik',img:'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=600&q=80'},
      {id:'pr2',judul:'Juara 1 Kaligrafi',level:'Kecamatan • 2025',lokasi:'Kec. Sumberjaya',kat:'seni',img:'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=600&q=80'},
      {id:'pr3',judul:'Juara 2 Futsal Antar MDU',level:'Kabupaten • 2025',lokasi:'Kab. Majalengka',kat:'olahraga',img:'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=600&q=80'},
      {id:'pr4',judul:'Juara Harapan Marawis',level:'Provinsi • 2024',lokasi:'Jawa Barat',kat:'seni',img:'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600&q=80'},
      {id:'pr5',judul:'Juara 1 Cerdas Cermat Islami',level:'Kecamatan • 2024',lokasi:'Kec. Sumberjaya',kat:'akademik',img:'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80'},
      {id:'pr6',judul:'Juara 3 Panahan Tradisional',level:'Kabupaten • 2023',lokasi:'Kab. Majalengka',kat:'olahraga',img:'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&q=80'}
    ],
    testimoni:[
      {id:'t1',nama:'Ibu Siti Aminah',peran:'Wali Santri • 2024',foto:'https://i.pravatar.cc/120?img=5',teks:'Alhamdulillah anak saya hafal juz 30 dalam 1 tahun. Gurunya sabar dan metode tahfidznya menyenangkan.'},
      {id:'t2',nama:'Rizki Maulana',peran:'Alumni 2023 • MTsN 1 Majalengka',foto:'https://i.pravatar.cc/120?img=8',teks:'Di MDU saya belajar marawis dan kaligrafi. Sekarang berani tampil di depan umum. Terima kasih ustadz/ustadzah!'},
      {id:'t3',nama:'Bpk. Dedi Hermawan',peran:'Wali Santri • 2025',foto:'https://i.pravatar.cc/120?img=9',teks:'Lingkungannya islami, fasilitas lengkap. Anak jadi lebih disiplin sholat dan cinta Al-Qur\'an.'}
    ],
    asatidz:[
      {id:'a1',nama:'Ust. Ahmad Fauzi, S.Pd.I',jabatan:'Kepala MDU',foto:'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80',sambutan:'Assalamu\'alaikum, mari bersama mencetak generasi Qur\'ani yang berkarakter.'},
      {id:'a2',nama:'Ust. Syarif Hidayat',jabatan:'Wakil Kepala',foto:'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80',sambutan:'Pendidikan diniyah fondasi akhlak mulia.'},
      {id:'a3',nama:'Ust. Fatimah Zahra',jabatan:'Guru Tahfidz',foto:'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&q=80',sambutan:'Tahfidz menyenangkan untuk santri sejak dini.'},
      {id:'a4',nama:'Ust. Ridwan Kamil',jabatan:'Guru Fiqih',foto:'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&q=80',sambutan:'Fiqih praktis membekali ibadah sehari-hari.'}
    ],
    inbox:[]
  },
  get apiBase(){ return location.pathname.includes('/admin') ? '../api' : 'api'; },
  uid:()=>'id'+Math.random().toString(36).slice(2,8)+Date.now().toString(36).slice(-3),
  _lsGet(k,def){ try{let v=JSON.parse(localStorage.getItem(k)); return v??def}catch{return def} },
  _lsSet(k,v){ try{localStorage.setItem(k,JSON.stringify(v))}catch{} },

  // Try API first, fallback to localStorage
  async load(){
    try{
      const r=await fetch(this.apiBase+'/data.php?'+Date.now(),{credentials:'same-origin',cache:'no-store',headers:{'Cache-Control':'no-cache','Pragma':'no-cache'}});
      if(r.ok){
        const j=await r.json();
        // normalize site shape (stats may be assoc)
        if(j.site && Array.isArray(j.site.stats) && j.site.stats[0] && !j.site.stats[0].v && j.site.stats[0][0]) j.site.stats=j.site.stats;
        this._lsSet('_mdu_cache',j);
        return this._mergeDefaults(j);
      }
    }catch(e){}
    // fallback: localStorage cache or defaults
    let c=this._lsGet('_mdu_cache',null);
    if(c) return this._mergeDefaults(c);
    let ls=this._lsGet('mdu_data_v1',null);
    if(ls) return this._mergeDefaults(ls);
    return this.defaults;
  },
  _mergeDefaults(d){
    return {...this.defaults,...d, site:{...this.defaults.site,...(d.site||{}), sambutan:{...this.defaults.site.sambutan,...(d.site?.sambutan||{})}, sambutanAmm:{...this.defaults.site.sambutanAmm,...(d.site?.sambutanAmm||{})}, kontak:{...this.defaults.site.kontak,...(d.site?.kontak||{})}, heroSlides:d.site?.heroSlides||this.defaults.site.heroSlides, stats:d.site?.stats||this.defaults.site.stats, misi:d.site?.misi||this.defaults.site.misi }};
  },
  // API helpers
  async api(path, opts={}){
    opts.credentials='same-origin';
    opts.headers={...(opts.headers||{}),'Content-Type':'application/json'};
    const r=await fetch(this.apiBase+path, opts);
    const j=await r.json().catch(()=>({}));
    if(!r.ok) throw new Error(j.error||'Request failed');
    return j;
  },
  // auth (server session)
  async login(u,p){
    const j=await this.api('/auth.php',{method:'POST',body:JSON.stringify({username:u,password:p})});
    return j.user;
  },
  async me(){
    try{ const j=await this.api('/auth.php?action=me'); return j.user; }catch{ return null; }
  },
  async logout(){ try{await this.api('/auth.php',{method:'POST',body:JSON.stringify({action:'logout'})})}catch{} },
  // legacy compat for old admin code
  async check(u,p){ return this.login(u,p); },
  session(){ return null; }, // deprecated: use me()
  setSession(){}, clearSession(){},
  async requireAuth(){
    const u=await this.me();
    if(!u) location.href='login.html';
    return u;
  }
};
