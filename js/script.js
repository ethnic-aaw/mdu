// ponytail: vanilla JS only; add framework when CMS/backend needed
try{ AOS.init({duration:700,once:false,mirror:true,offset:80}); }catch(e){}

// Navbar: hamburger + scrollspy
const navLinks=document.getElementById('navLinks'), hamburger=document.getElementById('hamburger');
hamburger?.addEventListener('click',()=>navLinks.classList.toggle('open'));
document.querySelectorAll('#navLinks a').forEach(a=>a.addEventListener('click',()=>navLinks.classList.remove('open')));
const sections=[...document.querySelectorAll('section[id]')], navAs=[...document.querySelectorAll('.nav-links a')];
const spy=()=>{const y=scrollY+100;let id=sections[0]?.id;sections.forEach(s=>{if(y>=s.offsetTop)id=s.id});navAs.forEach(a=>a.classList.toggle('active',a.getAttribute('href')==='#'+id))};
addEventListener('scroll',spy,{passive:true}); spy();

// ---- Render from Store (API-backed) ----
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function toSrc(u){ if(!u) return ''; if(u.startsWith('http')||u.startsWith('data:')||u.startsWith('/')) return u; if(u.startsWith('uploads/')) return u; return u; }
function normalizeStats(stats){
  if(!Array.isArray(stats)) return Store.defaults.site.stats;
  return stats.map(s=> s.v!==undefined ? s : {v:s['v']||s[Object.keys(s)[0]], l:s['l']||s[Object.keys(s)[1]]||''});
}
let testiTimer, testiIdx=0; let _beritaCache=(typeof Store!=='undefined'? Store.defaults.berita.slice():[]);
let heroTimer=null, heroIdx=0;
function renderHeroSlides(urls){
  const wrap=document.getElementById('heroSlides'), dotsWrap=document.getElementById('heroDots');
  if(!wrap) return;
  clearInterval(heroTimer);
  const list=(Array.isArray(urls)&&urls.length?urls:Store.defaults.site.heroSlides).filter(Boolean).slice(0,8);
  wrap.innerHTML=list.map((u,i)=>`<div class="hero-slide${i===0?' active':''}" style="background-image:url('${esc(toSrc(u))}')"></div>`).join('');
  if(dotsWrap){
    dotsWrap.innerHTML=list.length>1?list.map((_,i)=>`<button class="hero-dot${i===0?' active':''}" aria-label="Slide ${i+1}"></button>`).join(''):'';
    dotsWrap.querySelectorAll('.hero-dot').forEach((d,i)=>d.onclick=()=>{heroIdx=i;showHero(i);restartHero()});
  }
  const slides=[...wrap.querySelectorAll('.hero-slide')], dots=[...document.querySelectorAll('.hero-dot')];
  function showHero(i){heroIdx=(i+slides.length)%slides.length;slides.forEach((s,k)=>s.classList.toggle('active',k===heroIdx));dots.forEach((x,k)=>x.classList.toggle('active',k===heroIdx))}
  function restartHero(){clearInterval(heroTimer);if(slides.length>1)heroTimer=setInterval(()=>showHero(heroIdx+1),4500)}
  heroIdx=0; showHero(0);
  if(slides.length>1) heroTimer=setInterval(()=>showHero(heroIdx+1),4500);
  wrap.onmouseenter=()=>clearInterval(heroTimer);
  wrap.onmouseleave=restartHero;
}
function renderFromData(d){
  renderHeroSlides(d.site.heroSlides);
  // hero — sanitize: allow only span/br/em in title
  const hTitle=document.querySelector('.hero-content h1'); if(hTitle){
    const tmp=document.createElement('div'); tmp.innerHTML=d.site.heroTitle||'';
    tmp.querySelectorAll('*').forEach(el=>{ if(!['SPAN','BR','EM','B','STRONG','I'].includes(el.tagName)) el.replaceWith(...el.childNodes); });
    hTitle.innerHTML=tmp.innerHTML;
  }
  const slogan=document.querySelector('.hero-content .slogan'); if(slogan) slogan.textContent=d.site.slogan;
  const hDesc=document.querySelector('.hero-desc'); if(hDesc) hDesc.textContent=d.site.heroDesc;
  const stats=document.querySelector('.hero-stats'); if(stats){
    const ns=normalizeStats(d.site.stats);
    stats.innerHTML=ns.map(s=>`<div><b>${esc(s.v)}</b><span>${esc(s.l)}</span></div>`).join('');
  }
  // tentang
  const tTitle=document.querySelector('#tentang h2'); if(tTitle) tTitle.innerHTML=d.site.tentangTitle;
  const tDesc=document.querySelector('#tentang .grid-2 > div > p'); if(tDesc) tDesc.textContent=d.site.tentangDesc;
  const visiP=document.querySelector('.visi-card p'); if(visiP) visiP.textContent=d.site.visi;
  const misiUl=document.querySelector('.misi-list'); if(misiUl) misiUl.innerHTML=(d.site.misi||[]).map(m=>`<li><i class="fa-solid fa-check"></i> ${esc(m)}</li>`).join('');
  // sambutan — Mudirul Am (above) + Mudir
  const sA=d.site.sambutanAmm||{};
  const aImg=document.getElementById('sambutanAmmFoto'); if(aImg) aImg.src=toSrc(sA.foto);
  const aNama=document.getElementById('sambutanAmmNama'); if(aNama) aNama.textContent=sA.nama||'';
  const aJab=document.getElementById('sambutanAmmJabatan'); if(aJab) aJab.textContent=sA.jabatan||'';
  const aJudul=document.getElementById('sambutanAmmJudul'); if(aJudul) aJudul.textContent=sA.judul||'';
  const aP1=document.getElementById('sambutanAmmP1'); if(aP1) aP1.textContent=sA.p1||'';
  const aP2=document.getElementById('sambutanAmmP2'); if(aP2) aP2.textContent=sA.p2||'';
  const aP3=document.getElementById('sambutanAmmP3'); if(aP3) aP3.textContent=sA.p3||'';
  const sImg=document.querySelector('#sambutan .sambutan-photo img'); if(sImg) sImg.src=toSrc(d.site.sambutan.foto);
  const sName=document.querySelector('#sambutan .sambutan-name b'); if(sName) sName.textContent=d.site.sambutan.nama;
  const sJab=document.querySelector('#sambutan .sambutan-name span'); if(sJab) sJab.textContent=d.site.sambutan.jabatan;
  const sJudul=document.querySelector('#sambutan .sambutan-text h3'); if(sJudul) sJudul.textContent=d.site.sambutan.judul;
  const sPs=[...document.querySelectorAll('#sambutan .sambutan-text p')]; if(sPs[0]) sPs[0].textContent=d.site.sambutan.p1; if(sPs[1]) sPs[1].textContent=d.site.sambutan.p2; if(sPs[2]) sPs[2].textContent=d.site.sambutan.p3;
  // kontak info
  const infoPs=[...document.querySelectorAll('.kontak-info .info-item p')];
  if(infoPs[0]) infoPs[0].textContent=d.site.kontak.alamat;
  if(infoPs[1]) infoPs[1].innerHTML=`<a href="tel:${(d.site.kontak.tel||'').replace(/[^0-9+]/g,'')}">${esc(d.site.kontak.tel)}</a> • <a href="https://wa.me/${(d.site.kontak.tel||'').replace(/[^0-9]/g,'')}" target="_blank">Chat WA</a>`;
  if(infoPs[2]) infoPs[2].innerHTML=`<a href="mailto:${esc(d.site.kontak.email)}">${esc(d.site.kontak.email)}</a>`;
  // berita — Baca Selengkapnya buka modal detail
  _beritaCache=d.berita||[];
  const beritaGrid=document.querySelector('#berita .grid-3');
  if(beritaGrid) beritaGrid.innerHTML=(d.berita||[]).map((b,i)=>`
    <article class="news-card" data-aos="fade-up" data-aos-delay="${i*100}">
      <div class="news-thumb"><img src="${esc(toSrc(b.img))}" loading="lazy" alt="" onerror="this.style.display='none'"><span class="news-date"><i class="fa-regular fa-calendar"></i> ${esc(b.tgl)}</span></div>
      <div class="news-body"><span class="tag ${esc(b.tagClass||'')}">${esc(b.tag)}</span><h4>${esc(b.judul)}</h4><p>${esc(b.excerpt)}</p><button type="button" class="read-more" onclick="openBeritaModal('${esc(b.id)}')">Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i></button></div>
    </article>`).join('') || '<p style="color:var(--muted)">Belum ada berita.</p>';
  // program
  const progGrid=document.querySelector('.prog-grid');
  if(progGrid) progGrid.innerHTML=(d.program||[]).map((p,i)=>`
    <div class="prog-card ${esc(p.feat||'')}" data-aos="fade-up" data-aos-delay="${i*40}"><i class="fa-solid ${esc(p.icon)}"></i><h4>${esc(p.judul)}</h4><p>${esc(p.desc)}</p></div>`).join('');
  // fasilitas
  const fasGrid=document.querySelector('.fasilitas-grid');
  if(fasGrid) fasGrid.innerHTML=(d.fasilitas||[]).map((f,i)=>`
    <div class="fas-card ${esc(f.wide||'')}" data-aos="zoom-in" data-aos-delay="${i*50}"><img src="${esc(toSrc(f.img))}" loading="lazy" alt="" onerror="this.style.display='none'"><div class="fas-overlay"><h4><i class="fa-solid ${esc(f.icon)}"></i> ${esc(f.judul)}</h4><p>${esc(f.desc)}</p></div></div>`).join('');
  // ekskul
  const ekTrack=document.getElementById('ekskulTrack');
  if(ekTrack) ekTrack.innerHTML=(d.ekskul||[]).map(e=>`
    <div class="ekskul-card"><span class="ekskul-icon" style="background:${esc(e.color)}"><i class="fa-solid ${esc(e.icon)}"></i></span><h4>${esc(e.judul)}</h4><p>${esc(e.desc)}</p></div>`).join('');
  // prestasi
  const prGrid=document.getElementById('prestasiGrid');
  if(prGrid) prGrid.innerHTML=(d.prestasi||[]).map((p,i)=>`
    <div class="prestasi-card" data-cat="${esc(p.kat)}" data-aos="fade-up" data-aos-delay="${i*40}"><img src="${esc(toSrc(p.img))}" loading="lazy" alt="" onerror="this.style.display='none'"><div class="prestasi-info"><span class="lvl">${esc(p.level)}</span><h4>${esc(p.judul)}</h4><p>${esc(p.lokasi)}</p></div></div>`).join('') || '<p style="color:var(--muted)">Belum ada prestasi.</p>';
  // testimoni
  const tTrack=document.getElementById('testiTrack'), tDots=document.getElementById('testiDots');
  if(tTrack) tTrack.innerHTML=(d.testimoni||[]).map(t=>`
    <div class="testi-card"><img src="${esc(toSrc(t.foto))}" alt="" onerror="this.style.display='none'"><p>“${esc(t.teks)}”</p><b>${esc(t.nama)}</b><span>${esc(t.peran)}</span></div>`).join('') || '<div class="testi-card"><p>Belum ada testimoni.</p></div>';
  if(tDots) tDots.innerHTML=(d.testimoni||[]).map((_,i)=>`<span class="dot ${i===0?'active':''}"></span>`).join('');
  const aGrid=document.getElementById('asatidzGrid');
  if(aGrid) aGrid.innerHTML=(d.asatidz||[]).map(a=>`
    <div class="asatidz-card" data-aos="fade-up"><img src="${esc(toSrc(a.foto))}" loading="lazy" alt="${esc(a.nama)}" onerror="this.style.display='none'"><div class="asatidz-card-body"><b>${esc(a.nama)}</b><small>${esc(a.jabatan||'')}</small><p>${esc(a.sambutan||'')}</p></div></div>`).join('') || '<p style="color:var(--muted);grid-column:1/-1;text-align:center">Belum ada data asatidz.</p>';
  if(window.AOS) AOS.refresh();
  initSliders();
}
function initSliders(){
  const ekTrack=document.getElementById('ekskulTrack');
  const prev=document.getElementById('ekskulPrev'), next=document.getElementById('ekskulNext');
  if(prev&&ekTrack) prev.onclick=()=>ekTrack.scrollBy({left:-300,behavior:'smooth'});
  if(next&&ekTrack) next.onclick=()=>ekTrack.scrollBy({left:300,behavior:'smooth'});
  const tabs=[...document.querySelectorAll('#filterTabs .tab')];
  tabs.forEach(t=>{
    const nt=t.cloneNode(true); if(t.parentNode) t.parentNode.replaceChild(nt,t);
  });
  const freshTabs=[...document.querySelectorAll('#filterTabs .tab')];
  freshTabs.forEach(t=>t.addEventListener('click',()=>{
    freshTabs.forEach(x=>x.classList.remove('active')); t.classList.add('active');
    const f=t.dataset.filter;
    const freshCards=[...document.querySelectorAll('.prestasi-card')];
    freshCards.forEach(c=>c.style.display=(f==='all'||c.dataset.cat===f)?'':'none');
  }));
  const track=document.getElementById('testiTrack'), dots=[...document.querySelectorAll('#testiDots .dot')];
  if(!track||!dots.length) return;
  clearInterval(testiTimer); testiIdx=0;
  const go=i=>{testiIdx=(i+dots.length)%dots.length; track.style.transform=`translateX(-${testiIdx*100}%)`; dots.forEach((d,k)=>d.classList.toggle('active',k===testiIdx))};
  const play=()=>testiTimer=setInterval(()=>go(testiIdx+1),4000);
  dots.forEach((d,i)=>d.onclick=()=>{go(i);clearInterval(testiTimer);play()});
  const tp=document.getElementById('testiPrev'), tn=document.getElementById('testiNext');
  if(tp) tp.onclick=()=>{go(testiIdx-1);clearInterval(testiTimer);play()};
  if(tn) tn.onclick=()=>{go(testiIdx+1);clearInterval(testiTimer);play()};
  go(0); play();
  track.onmouseenter=()=>clearInterval(testiTimer);
  track.onmouseleave=play;
}

// Berita — modal detail untuk "Baca Selengkapnya"
function openBeritaModal(id){
  const b=_beritaCache.find(x=>x.id===id); if(!b) return;
  const m=document.getElementById('beritaModal'); if(!m) return;
  const img=document.getElementById('bmImg');
  if(b.img){ img.src=toSrc(b.img); img.style.display=''; } else img.style.display='none';
  const tagEl=document.getElementById('bmTag'); tagEl.textContent=b.tag||''; tagEl.className='tag '+(b.tagClass||'');
  document.getElementById('bmTgl').innerHTML='<i class="fa-regular fa-calendar"></i> '+esc(b.tgl||'');
  document.getElementById('bmJudul').textContent=b.judul||'';
  document.getElementById('bmExcerpt').textContent=b.excerpt||'';
  document.getElementById('bmKonten').textContent=b.konten||b.excerpt||'';
  m.classList.add('open'); m.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden';
}
function closeBeritaModal(){
  const m=document.getElementById('beritaModal'); if(!m) return;
  m.classList.remove('open'); m.setAttribute('aria-hidden','true'); document.body.style.overflow='';
}
window.openBeritaModal=openBeritaModal; window.closeBeritaModal=closeBeritaModal;
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeBeritaModal(); });

// hero fallback immediate so screenshot never blank even if API hangs
renderHeroSlides((typeof Store!=='undefined'&&Store.defaults&&Store.defaults.site&&Store.defaults.site.heroSlides)?Store.defaults.site.heroSlides:[]);
Store.load().then(renderFromData).catch(()=>renderFromData(Store.defaults));
// auto-refresh berita when tab refocused (fixes stale cache after admin save on Docker)
document.addEventListener('visibilitychange',()=>{ if(!document.hidden) Store.load().then(renderFromData).catch(()=>{}); });
window.addEventListener('focus',()=> Store.load().then(renderFromData).catch(()=>{}));

// Form -> POST to api/inbox.php (with localStorage fallback when file://)
const form=document.getElementById('contactForm'), msg=document.getElementById('formMsg');
const isEmail=v=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), isPhone=v=>/^0\d{9,13}$/.test(v.replace(/[\s-]/g,''));
form?.addEventListener('submit',async e=>{
  e.preventDefault(); msg.textContent=''; msg.className='form-msg';
  const fd=new FormData(form), nama=fd.get('nama')?.trim(), kontak=fd.get('kontak')?.trim(), pesan=fd.get('pesan')?.trim(), subjek=fd.get('subjek');
  let err=''; form.querySelectorAll('.invalid').forEach(el=>el.classList.remove('invalid'));
  if(!nama){err='Nama wajib diisi.'; form.nama.classList.add('invalid')}
  else if(!kontak||(!isEmail(kontak)&&!isPhone(kontak))){err='Email/No. HP tidak valid.'; form.kontak.classList.add('invalid')}
  else if(!pesan||pesan.length<10){err='Pesan minimal 10 karakter.'; form.pesan.classList.add('invalid')}
  if(err){msg.textContent=err; msg.classList.add('err'); return}
  msg.textContent='Mengirim...';
  try{
    const r=await fetch((Store.apiBase||'api')+'/inbox.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({nama,kontak,subjek,pesan})});
    if(!r.ok) throw new Error((await r.json().catch(()=>({}))).error||'Gagal');
    msg.textContent='✓ Terima kasih! Pesan terkirim — admin akan menindaklanjuti.'; msg.classList.add('ok');
    form.reset();
  }catch{
    // fallback: local cache so admin still sees it when offline
    let c; try{c=JSON.parse(localStorage.getItem('_mdu_cache')||'{}')}catch{c={}}
    c.inbox=c.inbox||[]; c.inbox.unshift({id:Store.uid(),nama,kontak,subjek,pesan,tgl:new Date().toISOString().slice(0,10),read:0});
    try{localStorage.setItem('_mdu_cache',JSON.stringify(c))}catch{}
    msg.textContent='✓ Pesan tersimpan (offline). Akan sinkron saat online.'; msg.classList.add('ok');
    form.reset();
  }
  setTimeout(()=>msg.textContent='',4000);
});
console.assert(document.querySelectorAll('section[id]').length>=10,'sections <10');
