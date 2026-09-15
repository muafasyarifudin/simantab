# Catatan Rilis & Riwayat Perubahan (Changelog)

Semua perubahan penting pada aplikasi **SIMANTAP (Sistem Manajemen Surat dan Penugasan Terpadu)** didokumentasikan dalam berkas ini.

Format pencatatan mengacu pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan mengikuti standar [Semantic Versioning (SemVer)](https://semver.org/lang/id/).

---

## [1.2.1] - 2026-09-15

### 🚀 Ditambahkan (Added)
- **Kustomisasi Ukuran Menyeluruh (Full-Grain Size Customization):**
  - Opsi pemilihan ukuran font, jarak baris, margin, dan dimensi untuk **seluruh elemen surat**:
    - **Header & KOP:** Judul heading naskah, nama lembaga induk (baris 1), satker/fakultas (baris 2), alamat, kontak, margin lembar A4 (compact, normal, spacious), jarak spasi KOP, dan ketebalan garis pembatas.
    - **Identitas & Metadata:** Font size metadata (nomor, sifat, lampiran, perihal) dan ukuran font tanggal surat.
    - **Tujuan & Isi Surat:** Font size penerima (Yth.), salam pembuka, paragraf pembuka, isi pokok surat, spasi antar-paragraf, jarak spasi baris (*line height* 1.2 hingga 2.0), paragraf penutup, dan salam penutup.
    - **Tanda Tangan, Cap & Footer:** Font size jabatan, nama pejabat, NIP/NIDN/NBM, tinggi ruang tanda tangan (45px - 120px), dimensi gambar tanda tangan, diameter stempel dinas, opasitas cap (70% - 100%), font size tembusan, dan catatan kaki (*footer note*).
- **Auto-Save Lokal Canggih Tanpa Beban Server (Zero-Lag Debounced Local Auto-Save):**
  - Penyimpanan draf otomatis secara instan ke `localStorage` peramban dengan debounce 280ms.
  - **Bebas Beban Server:** Tidak mengirim HTTP request/I-O database saat pengguna mengetik, menjaga server tetap ringan dan bebas lag.
  - **Deteksi & Pemulihan Draf (Draft Recovery Banner):** Notifikasi pintar otomatis saat membuka menu *Buat Surat* jika ada draf yang belum disimpan dari sesi sebelumnya, lengkap dengan tombol *Pulihkan Draf* dan *Buang Draf*.
  - Pembersihan draf lokal otomatis ketika formulir berhasil disubmit ke database.

---

## [1.2.0] - 2026-09-15

### 🚀 Ditambahkan (Added)
- **Panel Notifikasi Geser Sisi Kanan (Off-Canvas Right Drawer):**
  - Implementasi laci notifikasi interaktif yang meluncur mulus dari sisi kanan layar saat tombol lonceng ditekan.
  - Tab navigasi filter: *Semua*, *Persetujuan*, dan *Tugas*.
  - Indikator status baca/belum dibaca dengan tombol *Tandai Sudah Dibaca*.
  - Notifikasi suara halus (*chime*) saat interaksi.
  - Dukungan penutupan via tombol silang, klik backdrop, maupun tombol keyboard `Esc`.
- **Halaman Riwayat Versi & Catatan Rilis (Changelog UI):**
  - Halaman terdedikasi `?page=changelog` dengan tampilan linimasa (*timeline card*) yang interaktif dan modern.
  - Badge kategori terstandar: *Added*, *Fixed*, *Changed*, *Security*.
  - Navigasi cepat langsung terintegrasi pada menu *Administrasi* di Sidebar dan tautan footer aplikasi.

### 🔄 Diubah (Changed)
- **Sinkronisasi Badge Angka Sidebar & Dashboard Real-Time:**
  - Menghilangkan angka statis/dummy pada badge sidebar navigasi.
  - Badge *Semua Surat*, *Persetujuan*, *Penugasan*, dan *Laporan* kini dihitung langsung secara presisi berdasarkan query database aktif pengguna.

---

## [1.1.2] - 2026-09-15

### 🛠️ Diperbaiki (Fixed)
- **Perbaikan Error MySQL Packet (`2006 MySQL server has gone away`):**
  - Menyelesaikan kegagalan simpan draf saat pengguna menyematkan logo instansi atau tanda tangan beresolusi tinggi.
  - Payload SQL dibersihkan dari inline Base64 berukuran megabyte yang melampaui `max_allowed_packet` MySQL.

### 🔄 Diubah (Changed)
- **Arsitektur Penyimpanan Fisik Aset Surat:**
  - Metode `LetterService::saveBase64Image` dan `LetterService::upload` memisahkan file logo, tanda tangan, dan stempel langsung ke direktori fisik server `assets/uploads/letters/`.
  - Database kini hanya menyimpan path relatif pendek dan metadata ringan (JSON), menghemat konsumsi memori dan mempercepat kueri database secara drastis.
  - Validasi keamanan MIME type dan penamaan file acak (*cryptographic hash*) untuk mencegah eksploitasi unggahan berkas.

---

## [1.1.1] - 2026-09-15

### 🛠️ Diperbaiki (Fixed)
- **Perbaikan Hambatan Interaksi Tombol & Tab Editor (CSP Issue):**
  - Mengatasi masalah tombol tab, upload gambar, dan tombol kontrol yang tidak merespons akibat pemblokiran skrip *inline* oleh *Content Security Policy* (CSP).

### 🔄 Diubah (Changed)
- **Pemisahan Modul Skrip Studio Letter Builder:**
  - Seluruh logika interaktif editor dipindahkan dari skrip inline PHP ke berkas modul eksternal `assets/js/letter-builder.js`.
  - Penyesuaian aturan CSP pada `.htaccess` untuk mengizinkan pemuatan skrip mandiri yang aman dan font Google.

---

## [1.1.0] - 2026-09-15

### 🚀 Ditambahkan (Added)
- **Studio Pembuat Surat Layar Penuh (Full-Width Studio Builder):**
  - Layout kerja 2 kolom penuh (*split-pane*): panel formulir kustomisasi di kiri dan lembar kerja simulasi A4 (*live preview*) di kanan.
  - 4 Tab Konfigurasi:
    1. **KOP & Header:** Upload logo kiri/kanan, judul instansi 3 tingkat, alamat, nomor telepon/email, dan pilihan garis pembatas kop (Ganda tebal-tipis, Tebal tunggal, Garis tipis modern, Tanpa garis).
    2. **Nomor & Perihal:** Pilihan nomor otomatis/manual, lampiran, sifat (Biasa, Penting, Segera, Rahasia), dan tanggal terbit.
    3. **Isi Surat:** Salam pembuka preset, editor konten dengan toolbar format (Tebal, Miring, Garis Bawah, Rata Kiri/Tengah/Kanan/Justify, Indentasi), dan salam penutup.
    4. **Tanda Tangan & Footer:** Pemilihan pejabat/penandatangan, upload tanda tangan digital, upload stempel basah (dengan kontrol posisi dan transparansi), serta tembusan dinamis (*add/remove row*).
- **Kontrol Kanvas A4 Interaktif:**
  - Tombol kontrol zoom simulasi: *Fit to Width*, *75%*, *100%*, *125%*.
  - Pratinjau langsung secara real-time (*two-way data binding*) tanpa perlu reload halaman.
  - Status penyimpanan draf otomatis (*Auto-save indicator*).

---

## [1.0.0] - 2026-09-01

### 🚀 Ditambahkan (Added)
- **Rilis Perdana MVP SIMANTAP:**
  - Manajemen Dokumen Utama (Surat Tugas, Surat Keputusan, Surat Laporan Antar-Satuan Kerja).
  - Alur Kerja Persetujuan Berjenjang (*Multi-tier Approval Workflow*).
  - Autentikasi Pengguna & Role-Based Access Control (RBAC).
  - Penomoran Surat Otomatis Berdasarkan Klasifikasi Unit.
  - Lembar Verifikasi Publik dengan Validasi QR Code & Hash Keamanan.
  - Dashboard Operasional, Agenda Kegiatan, dan Audit Log Terpadu.
  - Kerangka Desain Responsif & Progressive Web App (PWA).

---

## 📌 Pedoman Pemversian (Versioning Guidelines)

SIMANTAP menggunakan format versi `MAJOR.MINOR.PATCH`:

1. **MAJOR (X.0.0):** Perubahan besar atau arsitektural yang bersifat *breaking changes* atau transformasi sistem yang masif.
2. **MINOR (0.X.0):** Penambahan fitur baru, modul baru, atau peningkatan signifikan yang tetap *backward-compatible*.
3. **PATCH (0.0.X):** Perbaikan *bug*, patch keamanan, atau perbaikan kecil pada performa dan UI.
