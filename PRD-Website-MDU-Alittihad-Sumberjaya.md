# Product Requirements Document (PRD)
## Website Resmi MDU AL-ITTIHAD SUMBERJAYA MAJALENGKA

| | |
|---|---|
| **Nama Proyek** | Website Sekolah MDU Al-Ittihad Sumberjaya Majalengka |
| **Jenis Dokumen** | Product Requirements Document (PRD) |
| **Versi** | 1.0 |
| **Tanggal** | 27 September 2026 |
| **Status** | Draft untuk Pengembangan |

---

## 1. Latar Belakang & Tujuan

MDU (Madrasah Diniyah Ula) Al-Ittihad Sumberjaya Majalengka adalah lembaga pendidikan keagamaan Islam nonformal tingkat dasar yang menjadi pelengkap pendidikan formal di Indonesia. Saat ini sekolah belum memiliki media informasi digital resmi yang representatif.

**Tujuan pembuatan website:**
- Menyediakan kanal informasi resmi dan terpercaya tentang profil, program, dan kegiatan sekolah.
- Meningkatkan citra sekolah sebagai lembaga yang modern, kredibel, dan berprestasi.
- Memudahkan calon siswa/wali murid mendapatkan informasi pendaftaran.
- Menjadi media publikasi berita, kegiatan, dan prestasi sekolah secara berkala.
- Menyediakan sarana kontak dan komunikasi yang mudah diakses.

## 2. Target Pengguna

| Segmen | Kebutuhan Utama |
|---|---|
| Calon siswa & orang tua/wali | Info pendaftaran, program, fasilitas, biaya |
| Siswa aktif | Info kegiatan, jadwal, ekstrakurikuler |
| Alumni | Nostalgia, testimoni, jejaring |
| Guru & staf | Media publikasi kegiatan sekolah |
| Masyarakat umum & Dinas terkait | Transparansi kelembagaan, kontak resmi |

## 3. Slogan & Identitas Visual

- **Slogan:** "Unggul dalam Prestasi, Berkarakter dalam Budaya"
- **Palet Warna:** Hijau (warna khas keislaman & MDU) sebagai warna primer, Putih sebagai warna sekunder/latar, dengan aksen emas/kuning tua opsional untuk elemen prestasi.
  - Hijau utama: `#0F5132` / `#1B7A43` (dapat disesuaikan dengan logo asli)
  - Hijau muda aksen: `#4CAF7D`
  - Putih/off-white: `#FFFFFF` / `#F8F9FA`
  - Teks gelap: `#1A1A1A`
- **Tipografi:** Font modern & mudah dibaca — judul menggunakan font tegas (contoh: Poppins/Montserrat), body text menggunakan font nyaman dibaca (contoh: Inter/Nunito Sans).
- **Gaya Desain:** Clean, minimalis, "youthful" namun tetap islami dan elegan — banyak ruang kosong (white space), ikon line-art sederhana, foto berkualitas tinggi.

## 4. Struktur Halaman (Sitemap) — Single Page Landing

Website dibangun sebagai **satu halaman utama (one-page scrolling)** dengan navigasi anchor link ke setiap section, dilengkapi navbar sticky.

```
Navbar (Sticky)
 ├── Logo + Nama Sekolah
 ├── Menu: Beranda | Tentang | Berita | Program | Fasilitas | Ekstrakurikuler | Prestasi | Testimoni | Kontak
 └── CTA Button: "Daftar Sekarang"

1. Hero Section
2. Tentang Sekolah (Sejarah, Visi, Misi)
3. Sambutan Kepala Sekolah
4. Berita Terbaru (3 item)
5. Program Akademik
6. Fasilitas
7. Ekstrakurikuler
8. Prestasi Sekolah (Galeri/Slideshow)
9. Testimoni
10. Hubungi Kami (Peta, Form, Kontak)
Footer
Back to Top Button (Floating)
```

## 5. Rincian Fungsional per Section

### 5.1 Hero Section
- Background: foto gedung sekolah resolusi tinggi (dengan overlay gradasi hijau transparan agar teks terbaca).
- Logo sekolah (posisi tengah atas atau di navbar).
- Judul besar: Nama sekolah lengkap.
- Slogan: "Unggul dalam Prestasi, Berkarakter dalam Budaya".
- 2–3 tombol CTA: **Daftar Sekarang**, **Lihat Prestasi**, **Hubungi Kami**.
- Animasi: fade-in/slide-up saat halaman dimuat, efek parallax ringan pada background (opsional).

### 5.2 Tentang Sekolah
- Sejarah singkat berdirinya MDU Al-Ittihad Sumberjaya.
- Visi (satu paragraf pernyataan cita-cita jangka panjang).
- Misi (list poin-poin strategi pencapaian visi).
- Layout: dua kolom (teks + gambar/ilustrasi) pada desktop, stack vertikal pada mobile.

### 5.3 Sambutan Kepala Sekolah
- Foto Kepala Sekolah.
- Nama & jabatan.
- Kutipan sambutan (2–4 paragraf, gaya hangat & mengundang).
- Desain: card dengan quote mark besar sebagai elemen dekoratif.

### 5.4 Berita Terbaru
- Grid 3 kolom (desktop) → 1 kolom (mobile) berisi 3 kartu berita/kegiatan terbaru.
- Setiap kartu: thumbnail gambar, tanggal, judul singkat, cuplikan (excerpt), tombol "Baca Selengkapnya".
- Data bersifat statis/dummy (dapat diganti manual) pada versi awal — struktur HTML disiapkan agar mudah dikonversi ke CMS/dinamis di kemudian hari.

### 5.5 Program Akademik
- Deskripsi singkat kurikulum/mata pelajaran diniyah (Al-Qur'an, Fiqih, Akidah Akhlak, Bahasa Arab, dsb. — sesuaikan data riil sekolah).
- Ditampilkan dalam bentuk icon-box grid (ikon + judul + deskripsi singkat).

### 5.6 Fasilitas
- Grid galeri (kartu bergambar) menampilkan minimal 5 fasilitas:
  1. Laboratorium
  2. Perpustakaan
  3. Masjid
  4. Aula
  5. Ruang Multimedia
- Setiap kartu: gambar, judul fasilitas, deskripsi 1–2 kalimat.
- Efek hover: zoom gambar halus + overlay keterangan.

### 5.7 Ekstrakurikuler
- Highlight kegiatan ekstrakurikuler (contoh: Marawis/Hadroh, Kaligrafi, Tahfidz, Pramuka, Muhadhoroh/Public Speaking, Olahraga).
- Format: carousel/slider atau grid card dengan ikon/foto kegiatan.

### 5.8 Prestasi Sekolah
- Galeri atau slideshow (carousel) penghargaan/piala/sertifikat.
- Kategori: Akademik, Olahraga, Seni — dapat menggunakan filter tab sederhana (JavaScript) untuk memilah kategori.
- Setiap item: foto penghargaan, nama lomba, tingkat (kecamatan/kabupaten/provinsi), tahun.

### 5.9 Testimoni
- Slider/carousel kutipan dari siswa dan alumni.
- Setiap testimoni: foto/avatar, nama, status (siswa/alumni tahun berapa), isi testimoni.
- Auto-slide dengan tombol navigasi manual (prev/next + dot indicator).

### 5.10 Hubungi Kami
- Alamat lengkap sekolah.
- Peta lokasi (embed Google Maps iframe).
- Nomor telepon & email (clickable: `tel:` dan `mailto:`).
- Ikon media sosial (opsional: Instagram/Facebook/WhatsApp).
- Formulir kontak sederhana: Nama, Email/No. HP, Subjek, Pesan, tombol Kirim.
  - Validasi input dasar dengan JavaScript (client-side only untuk versi awal; integrasi backend/email service menjadi pengembangan lanjutan).

### 5.11 Footer
- Logo & nama sekolah singkat.
- Link cepat (anchor ke section).
- Kontak singkat.
- Copyright & tahun.

### 5.12 Tombol Back to Top
- Muncul otomatis (fade-in) setelah user scroll melewati batas tertentu (misal 300px).
- Klik → smooth scroll ke atas halaman via JavaScript.

## 6. Kebutuhan Teknis (Technical Requirements)

| Aspek | Spesifikasi |
|---|---|
| **Bahasa/Teknologi** | HTML5, CSS3, JavaScript (Vanilla JS) |
| **Layout Engine** | CSS Flexbox & CSS Grid |
| **Responsivitas** | CSS Media Query — breakpoint Desktop (≥1024px), Tablet (768–1023px), Mobile (<768px) |
| **Animasi Scroll** | Library AOS (Animate On Scroll) via CDN |
| **Font** | Google Fonts (CDN) |
| **Ikon** | Font Awesome / Lucide Icons (CDN) |
| **Peta** | Google Maps Embed (iframe) |
| **Hosting file** | Struktur folder statis (index.html, /css, /js, /assets/img) |
| **Kompatibilitas Browser** | Chrome, Firefox, Edge, Safari (versi terbaru) |
| **Performa** | Gambar dikompresi/lazy-loaded agar loading cepat |

## 7. Struktur Folder Proyek

```
mdu-alittihad-website/
├── index.html
├── /css
│   └── style.css
├── /js
│   ├── script.js        → interaksi umum (navbar, form, slider)
│   └── back-to-top.js   → fitur back to top
├── /assets
│   ├── /img
│   │   ├── hero-bg.jpg
│   │   ├── logo.png
│   │   ├── fasilitas/
│   │   ├── prestasi/
│   │   └── testimoni/
│   └── /icons
└── README.md
```

## 8. Rencana Pengembangan Step by Step

1. **Setup struktur dasar** — buat file `index.html`, `style.css`, `script.js`, susun struktur folder aset.
2. **Bangun Navbar & Hero Section** — termasuk logo, slogan, dan CTA buttons.
3. **Bangun section "Tentang Sekolah" & "Sambutan Kepala Sekolah"** — layout dua kolom responsif.
4. **Bangun section "Berita Terbaru"** — grid card dengan Flexbox/Grid.
5. **Bangun section "Program Akademik" & "Fasilitas"** — icon-box grid & gallery grid.
6. **Bangun section "Ekstrakurikuler" & "Prestasi Sekolah"** — carousel/slider dengan JavaScript.
7. **Bangun section "Testimoni"** — slider otomatis + kontrol manual.
8. **Bangun section "Hubungi Kami"** — form kontak, embed peta, info kontak.
9. **Bangun Footer.**
10. **Implementasi Responsive Design** — uji tampilan di breakpoint Desktop/Tablet/Mobile menggunakan Media Query.
11. **Implementasi fitur Back to Top** dengan JavaScript (scroll event listener + smooth scroll).
12. **Integrasi AOS (Animate On Scroll)** — tambahkan atribut `data-aos` pada elemen-elemen section.
13. **Testing lintas perangkat & browser**, optimasi gambar dan performa loading.
14. **Review konten** (teks, foto asli sekolah) & finalisasi sebelum publish/hosting.

## 9. Kebutuhan Konten dari Pihak Sekolah (Belum Tersedia)

Agar pengembangan dapat lanjut ke tahap coding dengan data riil, pihak sekolah perlu menyediakan:
- Logo resmi sekolah (format PNG transparan).
- Foto gedung sekolah kualitas tinggi (untuk Hero Section).
- Teks sejarah, visi, dan misi resmi.
- Nama & foto Kepala Sekolah + naskah sambutan.
- Minimal 3 berita/kegiatan terbaru beserta foto.
- Daftar & deskripsi program akademik (mata pelajaran diniyah).
- Foto masing-masing fasilitas (Lab, Perpustakaan, Masjid, Aula, Ruang Multimedia).
- Daftar ekstrakurikuler beserta foto kegiatan.
- Foto/scan penghargaan & data lomba (nama, tingkat, tahun).
- Testimoni tertulis dari minimal 2–3 siswa/alumni beserta foto.
- Alamat lengkap, nomor telepon, email resmi, dan link Google Maps.

## 10. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Semua 9 section utama tampil sesuai urutan yang diminta.
- [ ] Website dapat diakses dan tampil rapi di Desktop, Tablet, dan Mobile.
- [ ] Navigasi navbar berfungsi (smooth scroll ke masing-masing section).
- [ ] Tombol CTA ("Daftar Sekarang", "Lihat Prestasi", "Hubungi Kami") berfungsi mengarah ke section/form yang sesuai.
- [ ] Fitur Back to Top muncul saat scroll dan berfungsi dengan smooth scroll.
- [ ] Animasi AOS berjalan halus saat elemen masuk viewport, tanpa mengganggu performa.
- [ ] Formulir kontak memiliki validasi dasar (field wajib, format email).
- [ ] Peta lokasi ter-embed dan menampilkan lokasi yang benar.
- [ ] Warna dan tipografi konsisten dengan identitas hijau-putih di seluruh halaman.
- [ ] Tidak ada elemen yang overflow/rusak pada ukuran layar kecil (mobile ≤375px).

## 11. Ruang Lingkup Lanjutan (Out of Scope untuk Versi 1.0)

- Sistem pendaftaran online dengan backend/database.
- Panel admin/CMS untuk mengelola berita secara dinamis.
- Sistem login siswa/orang tua.
- Integrasi pembayaran SPP online.

*(Item-item di atas dapat menjadi rencana pengembangan Fase 2.)*

---

**Disiapkan sebagai dasar pengembangan website MDU Al-Ittihad Sumberjaya Majalengka.**
