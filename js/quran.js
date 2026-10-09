// ponytail: client-only fetch equran.id; add DB cache when offline needed
(function(){
  const G=document.getElementById('quranSurahGrid'), R=document.getElementById('quranReader'),
    L=document.getElementById('quranAyatList'), T=document.getElementById('quranReaderTitle'),
    M=document.getElementById('quranReaderMeta'), B=document.getElementById('quranBismillah'),
    S=document.getElementById('quranSearch'),
    H=document.getElementById('quranCacheHint');
  if(!G) return;
  let surahs=[], cur=1;
  const esc=s=>String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  const cacheK=k=>'_quran_'+k;
  async function fetchSurahList(){
    try{
      const c=localStorage.getItem(cacheK('surahList'));
      if(c){ surahs=JSON.parse(c); renderGrid(surahs); }
    }catch{}
    try{
      const r=await fetch('https://equran.id/api/v2/surat',{cache:'no-store'});
      if(!r.ok) throw 0;
      const j=await r.json(); surahs=j.data||[];
      localStorage.setItem(cacheK('surahList'), JSON.stringify(surahs));
      renderGrid(surahs); H.textContent='✓ sinkron';
    }catch{
      if(!surahs.length) G.innerHTML='<p class="muted" style="grid-column:1/-1;text-align:center">Gagal muat. Cek koneksi.</p>';
    }
  }
  function renderGrid(list){
    const q=(S.value||'').toLowerCase().trim();
    const f=q?list.filter(x=> (x.namaLatin+' '+x.nama+' '+x.arti).toLowerCase().includes(q)):list;
    G.innerHTML=f.map(s=>`<button class="quran-card" onclick="quranOpen(${s.nomor})" data-aos="fade-up"><span class="quran-num">${s.nomor}</span><span><b>${esc(s.namaLatin)}</b><br><small>${esc(s.arti)} • ${s.jumlahAyat} ayat • ${esc(s.tempatTurun)}</small></span><span class="quran-arab">${esc(s.nama)}</span></button>`).join('')||'<p class="muted" style="grid-column:1/-1">Tidak ditemukan.</p>';
  }
  window.quranOpen=async function(nomor){
    cur=nomor; G.classList.add('hide'); R.classList.remove('hide');
    T.textContent='Memuat...'; L.innerHTML='<p class="muted">Memuat ayat...</p>'; M.textContent=''; B.style.display='';
    // hide bismillah for At-Taubah
    if(nomor===9) B.style.display='none'; else B.style.display='';
    try{
      const ck=cacheK('surat_'+nomor), cc=localStorage.getItem(ck);
      let data=null;
      if(cc){ try{ data=JSON.parse(cc);}catch{} }
      const r=await fetch('https://equran.id/api/v2/surat/'+nomor,{cache:'no-store'});
      if(r.ok){ data=await r.json().then(j=>j.data); localStorage.setItem(ck, JSON.stringify(data)); }
      if(!data) throw 0;
      T.textContent=data.namaLatin+' — '+data.nama;
      M.textContent=data.arti+' • '+data.jumlahAyat+' ayat • '+data.tempatTurun;
      if(data.nomor===1) B.style.display='none'; // Al-Fatihah bismillah is ayat 1
      L.innerHTML=(data.ayat||[]).map(a=>`
        <div class="quran-ayat">
          <div class="quran-ayat-meta"><span class="ayah-badge">${a.nomorAyat}</span><span>${esc(data.namaLatin)}:${a.nomorAyat}</span></div>
          <div class="quran-ayat-arab">${esc(a.teksArab)}</div>
          <div class="quran-ayat-tr">${esc(a.teksIndonesia)}</div>
        </div>`).join('');
      // prev/next
      document.getElementById('quranPrev').onclick=()=>{ if(cur>1) quranOpen(cur-1); };
      document.getElementById('quranNext').onclick=()=>{ if(cur<114) quranOpen(cur+1); };
      document.getElementById('quranPrev').style.visibility=cur>1?'':'hidden';
      document.getElementById('quranNext').style.visibility=cur<114?'':'hidden';
    }catch{
      T.textContent='Gagal memuat surah '+nomor; L.innerHTML='<p class="muted">Coba lagi.</p>';
    }
  };
  window.quranBack=function(){ R.classList.add('hide'); G.classList.remove('hide'); G.scrollIntoView({behavior:'smooth',block:'start'}); };
  S?.addEventListener('input',()=>renderGrid(surahs));
  fetchSurahList();
})();
