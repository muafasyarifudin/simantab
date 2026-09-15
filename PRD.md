# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Sistem Manajemen Surat dan Penugasan Terpadu (SIMANTAP)

| Informasi | Keterangan |
|---|---|
| Nama produk | SIMANTAP — Sistem Manajemen Surat dan Penugasan Terpadu |
| Jenis produk | Aplikasi web mobile-first dan Progressive Web App (PWA) |
| Versi dokumen | 1.2 (Pembaruan Studio Builder, Drawer Interaktif, & File Engine) |
| Status | Aktif dalam Pengembangan & Implementasi |
| Target pengguna | Instansi, lembaga, unit, divisi, dan satuan kerja |
| Platform awal | Web desktop, tablet, dan mobile |
| Platform lanjutan | Aplikasi Android dan iOS melalui API yang sama |
| Bahasa utama | Bahasa Indonesia |

---

## 1. Ringkasan Eksekutif

SIMANTAP adalah aplikasi manajemen surat dan penugasan lintas satuan kerja yang mengelola seluruh siklus dokumen resmi: penyusunan draf, pemeriksaan, persetujuan berjenjang, penomoran, penandatanganan, distribusi, disposisi, pelaksanaan tugas, pengumpulan laporan, hingga pengarsipan.

Aplikasi dirancang dengan pendekatan **mobile-first**, sehingga fungsi utama tetap nyaman digunakan dari telepon seluler. Pengguna dapat membuat draf, menerima surat, menyetujui dokumen, melihat agenda, menerima pengingat, mengunggah bukti kegiatan, dan menyelesaikan laporan tanpa harus menggunakan komputer.

Produk tidak hanya berfungsi sebagai generator surat. Setiap surat menjadi objek kerja yang terhubung dengan pihak pengirim, penerima, pejabat penandatangan, kegiatan, tenggat waktu, tindak lanjut, laporan, dan jejak audit.

---

## 2. Latar Belakang

Pengelolaan surat antar-satuan kerja sering dilakukan menggunakan kombinasi aplikasi pengolah kata, pesan instan, email, spreadsheet, dan arsip lokal. Pola ini menimbulkan beberapa masalah:

1. Format dan penomoran surat tidak konsisten.
2. Status persetujuan sulit dipantau.
3. Dokumen mudah terduplikasi atau hilang.
4. Pimpinan kesulitan menyetujui surat ketika tidak berada di depan komputer.
5. Penerima surat tugas dapat melewatkan jadwal kegiatan.
6. Laporan pelaksanaan tidak selalu terhubung dengan surat tugas asalnya.
7. Riwayat perubahan dan distribusi dokumen tidak terdokumentasi dengan baik.
8. Pencarian arsip membutuhkan waktu lama.
9. Dokumen rahasia berisiko diakses oleh pihak yang tidak berwenang.
10. Pimpinan tidak memiliki gambaran menyeluruh mengenai beban tugas dan kinerja tindak lanjut setiap unit.

SIMANTAP dibuat untuk menyatukan proses tersebut dalam satu sistem yang terstruktur, aman, dapat ditelusuri, dan mendukung penggunaan mobile secara penuh.

---

## 3. Visi Produk

Menjadi pusat administrasi surat, keputusan, penugasan, komunikasi formal, pelaporan, dan arsip digital lintas satuan kerja yang mudah digunakan, transparan, aman, dan dapat diakses dari perangkat apa pun.

---

## 4. Tujuan Produk

### 4.1 Tujuan utama

- Mempercepat pembuatan dan persetujuan surat resmi.
- Menstandarkan format, nomor, klasifikasi, dan alur surat.
- Menghubungkan surat tugas dengan agenda, notifikasi, pelaksanaan, dan laporan.
- Mempermudah komunikasi formal antar-satuan kerja.
- Menyediakan arsip digital yang cepat dicari dan terkontrol.
- Memberikan visibilitas status dokumen secara real-time.
- Memungkinkan pimpinan melakukan persetujuan dengan nyaman melalui perangkat mobile.
- Menyediakan data operasional untuk evaluasi layanan administrasi.

### 4.2 Sasaran keberhasilan

- Minimal 90% surat internal dibuat melalui sistem setelah masa adopsi.
- Waktu rata-rata persetujuan surat berkurang minimal 50%.
- Seluruh nomor surat resmi tercatat tanpa duplikasi.
- Minimal 95% surat tugas memiliki status tindak lanjut yang dapat dilacak.
- Minimal 90% laporan pelaksanaan disampaikan sebelum batas waktu.
- Pencarian surat dapat diselesaikan dalam waktu kurang dari 10 detik.
- Seluruh perubahan, persetujuan, unduhan penting, dan distribusi tercatat dalam audit log.

---

## 5. Ruang Lingkup Produk

### 5.1 Termasuk dalam MVP

- Autentikasi dan manajemen sesi.
- Struktur instansi dan satuan kerja.
- Data pengguna, pegawai, jabatan, dan pejabat.
- Role-Based Access Control (RBAC).
- Pembuatan Surat Tugas.
- Pembuatan Surat Keputusan.
- Pembuatan surat/laporan resmi antar-satuan kerja.
- Template dan komponen surat.
- Persetujuan berjenjang.
- Catatan, penolakan, dan revisi.
- Penomoran otomatis.
- Pembuatan dokumen PDF.
- Distribusi surat kepada pengguna dan unit.
- Notifikasi dalam aplikasi dan email.
- Agenda dari surat tugas.
- Laporan pelaksanaan tugas.
- Lampiran dokumen dan foto.
- QR Code verifikasi dokumen.
- Arsip dan pencarian.
- Dashboard operasional.
- Audit log.
- Tampilan responsif mobile-first.

### 5.2 Pengembangan tahap berikutnya

- Surat masuk dari pihak eksternal.
- Surat keluar eksternal.
- Disposisi surat masuk.
- Tanda tangan elektronik tersertifikasi.
- Integrasi WhatsApp gateway.
- Web Push Notification.
- Integrasi sistem kepegawaian.
- Integrasi kalender eksternal.
- OCR dokumen hasil pindai.
- Retensi dan pemusnahan arsip.
- Aplikasi native/cross-platform Android dan iOS.
- Analitik lanjutan dan pengukuran SLA.

### 5.3 Di luar ruang lingkup MVP

- Sistem keuangan dan pencairan biaya perjalanan dinas.
- Penggajian dan presensi pegawai.
- Penyimpanan sertifikat elektronik milik pengguna tanpa penyedia resmi.
- Pengiriman surat fisik melalui jasa kurir.
- Pengelolaan seluruh proses SDM di luar konteks surat dan penugasan.

---

## 6. Definisi Istilah

| Istilah | Definisi |
|---|---|
| Instansi | Organisasi induk yang menggunakan sistem |
| Satuan kerja | Unit, lembaga, fakultas, biro, bagian, atau divisi di bawah instansi |
| Surat Tugas | Surat yang memberikan penugasan kepada satu atau lebih orang |
| Surat Keputusan | Dokumen keputusan resmi yang memuat konsideran dan diktum |
| Surat laporan | Surat resmi antar-unit yang menyampaikan laporan periodik atau insidental |
| Laporan pelaksanaan | Laporan tindak lanjut dari kegiatan yang berasal dari Surat Tugas |
| Disposisi | Instruksi pimpinan kepada unit atau pegawai untuk menindaklanjuti surat |
| Draf | Dokumen yang belum diajukan untuk pemeriksaan atau persetujuan |
| Approver | Pengguna yang berwenang menyetujui atau menolak dokumen |
| Verifikator | Pengguna yang memeriksa format, kelengkapan, dan kepatuhan administratif |
| Penandatangan | Pejabat yang mengesahkan surat |
| Tembusan | Pihak yang menerima salinan surat sebagai informasi |
| Delegasi | Pengalihan kewenangan sementara kepada pejabat lain |
| SLA | Target waktu penyelesaian suatu proses |

---

## 7. Pemangku Kepentingan

- Pimpinan instansi.
- Kepala lembaga/unit/satuan kerja.
- Bagian tata usaha atau administrasi umum.
- Bagian hukum atau pemeriksa kebijakan.
- Pembuat dan pengusul surat.
- Pegawai atau anggota yang menerima tugas.
- Administrator sistem.
- Auditor internal.
- Tim pengembang dan pengelola infrastruktur.

---

## 8. Persona Pengguna

### 8.1 Pembuat surat

Membutuhkan formulir sederhana, template baku, penyimpanan draf otomatis, pilihan penerima dari direktori, dan informasi jelas ketika surat perlu diperbaiki.

### 8.2 Tata usaha/verifikator

Membutuhkan antrean pemeriksaan, validasi kelengkapan, pengaturan nomor, pengecekan format, catatan revisi, dan kemampuan melihat perbandingan versi.

### 8.3 Pimpinan

Membutuhkan ringkasan surat, konteks pengajuan, lampiran, catatan pemeriksa, serta tombol setujui, tolak, atau kembalikan yang mudah digunakan dari mobile.

### 8.4 Pegawai penerima tugas

Membutuhkan notifikasi, rincian tugas, agenda, pengingat, navigasi lokasi, daftar anggota, dan formulir laporan kegiatan.

### 8.5 Administrator

Membutuhkan pengelolaan struktur organisasi, pengguna, hak akses, format surat, penomoran, alur persetujuan, integrasi, dan audit sistem.

### 8.6 Auditor

Membutuhkan pencarian terperinci, dokumen final, histori versi, persetujuan, distribusi, akses, dan bukti bahwa data tidak diubah secara tidak sah.

---

## 9. Peran dan Hak Akses

| Peran | Kewenangan utama |
|---|---|
| Super Admin | Mengelola seluruh instansi, konfigurasi global, keamanan, dan integrasi |
| Admin Instansi | Mengelola data master dan pengguna dalam satu instansi |
| Admin Satuan Kerja | Mengelola pengguna, template terbatas, dan surat unitnya |
| Tata Usaha | Memeriksa dokumen, mengelola nomor, distribusi, dan arsip |
| Bagian Hukum | Memeriksa aspek hukum Surat Keputusan jika diperlukan |
| Pimpinan | Menyetujui, menolak, mengembalikan, dan menandatangani surat |
| Kepala Satuan Kerja | Mengajukan, menyetujui internal, memantau surat, dan melihat laporan unit |
| Pembuat Surat | Membuat, mengedit draf, mengajukan, dan memperbaiki dokumen miliknya |
| Pegawai | Menerima surat, mengelola agenda, dan menyampaikan laporan |
| Auditor | Membaca dokumen serta audit log sesuai lingkup kewenangan |

Satu pengguna dapat mempunyai lebih dari satu peran. Hak akses harus dapat dibatasi berdasarkan instansi, satuan kerja, jenis surat, dan tingkat kerahasiaan.

---

## 10. Struktur Organisasi

Sistem harus mendukung organisasi bertingkat tanpa batas kedalaman praktis:

1. Instansi.
2. Unit utama/lembaga.
3. Satuan kerja/bagian.
4. Subbagian/divisi.
5. Pejabat dan pegawai.

Setiap unit minimal mempunyai kode, nama, singkatan, unit induk, pejabat aktif, alamat/kop, status aktif, dan konfigurasi penomoran. Perubahan pejabat tidak boleh mengubah informasi historis pada surat yang sudah diterbitkan.

---

## 11. Jenis Dokumen

### 11.1 Jenis awal

- Surat Tugas.
- Surat Keputusan.
- Surat Laporan Antar-Satuan Kerja.
- Nota Dinas.
- Surat Undangan.
- Surat Edaran.
- Surat Perintah.
- Surat Keterangan.
- Berita Acara.

MVP wajib menyelesaikan tiga jenis pertama. Jenis lainnya menggunakan mesin template yang sama dan dapat diaktifkan bertahap.

### 11.2 Atribut umum dokumen

- Jenis surat.
- Judul/perihal.
- Sifat: biasa, penting, segera, atau rahasia.
- Prioritas.
- Klasifikasi arsip.
- Unit pembuat dan pemilik dokumen.
- Tujuan/penerima.
- Tembusan.
- Tanggal surat.
- Tanggal berlaku.
- Penandatangan.
- Isi terstruktur.
- Lampiran.
- Status.
- Nomor dan kode verifikasi setelah disahkan.

---

## 12. Alur Utama Sistem

### 12.1 Alur umum surat

1. Pengguna memilih jenis surat.
2. Sistem memuat template dan alur persetujuan yang berlaku.
3. Pengguna mengisi data dan menyimpan draf.
4. Sistem memvalidasi kelengkapan.
5. Pengguna melihat pratinjau lalu mengajukan surat.
6. Verifikator memeriksa dokumen.
7. Dokumen dapat disetujui, ditolak, atau dikembalikan untuk revisi.
8. Setelah lolos pemeriksaan, dokumen diteruskan ke approver berikutnya.
9. Penandatangan memberikan pengesahan.
10. Sistem menghasilkan nomor final, PDF final, hash, dan QR Code.
11. Tata usaha atau sistem mendistribusikan surat.
12. Penerima memperoleh notifikasi dan surat masuk ke kotak masuknya.
13. Jika membutuhkan tindak lanjut, sistem memantau progres dan tenggat.
14. Dokumen dan hasil tindak lanjut masuk ke arsip.

### 12.2 Alur revisi

1. Verifikator/approver memilih **Kembalikan untuk revisi**.
2. Catatan revisi wajib diisi.
3. Status berubah menjadi **Perlu Revisi**.
4. Pembuat memperbaiki draf pada versi baru.
5. Sistem menyimpan versi lama sebagai histori.
6. Pembuat mengajukan ulang.
7. Alur dilanjutkan dari tahapan yang dikonfigurasi.

### 12.3 Alur pembatalan atau pencabutan

- Draf dapat dibatalkan oleh pembuat.
- Surat yang sudah disahkan tidak boleh dihapus.
- Surat final hanya dapat dicabut oleh pejabat berwenang dengan alasan wajib.
- Surat pengganti harus memiliki hubungan dengan surat yang dicabut.
- Halaman verifikasi publik menampilkan status **Dicabut** dan referensi pengganti jika tersedia.

---

## 13. Kebutuhan Fungsional

### FR-01 Autentikasi

- Login menggunakan username/email/NIP dan kata sandi.
- Mendukung lupa kata sandi.
- Mendukung pembatasan percobaan login.
- Sistem mengakhiri sesi yang tidak aktif.
- Administrator dapat menonaktifkan akun.
- Tahap lanjutan mendukung SSO dan autentikasi dua faktor.

### FR-02 Manajemen pengguna dan pegawai

- Tambah, ubah, nonaktifkan, dan impor pengguna dari Excel/CSV.
- Satu akun dapat terhubung dengan satu data pegawai.
- Data mencakup NIP/NIK internal, nama, gelar, kontak, jabatan, unit, dan status.
- Sistem menyimpan histori jabatan dan unit.
- Pengguna dapat memiliki beberapa peran.

### FR-03 Data instansi dan satuan kerja

- Mengelola struktur organisasi hierarkis.
- Mengelola kop surat, alamat, logo, kode unit, pejabat, dan stempel.
- Menetapkan unit induk dan cakupan akses.
- Menonaktifkan unit tanpa menghapus histori.

### FR-04 Template surat

- Administrator dapat membuat template per jenis surat dan unit.
- Template memiliki bagian wajib dan opsional.
- Mendukung placeholder seperti nomor, tanggal, nama pejabat, dan unit.
- Mendukung kop, footer, margin, font, tanda tangan, stempel, serta lampiran.
- Template lama tetap digunakan oleh surat historis.

### FR-05 Pembuatan draf

- Formulir disusun berdasarkan jenis dokumen.
- Draf tersimpan otomatis.
- Pengguna dapat melanjutkan draf dari perangkat berbeda.
- Pengguna dapat menduplikasi surat lama menjadi draf baru.
- Sistem menampilkan daftar bagian yang belum lengkap.
- Pengguna dapat melihat pratinjau sebelum mengajukan.

### FR-06 Surat Tugas

Data khusus minimal:

- Nama dan deskripsi kegiatan.
- Dasar penugasan.
- Daftar petugas.
- Peran/jabatan setiap petugas dalam kegiatan.
- Ketua atau penanggung jawab.
- Tanggal dan jam mulai/selesai.
- Lokasi fisik atau tautan daring.
- Sumber anggaran jika relevan.
- Instruksi tambahan.
- Batas pengumpulan laporan.
- Jenis laporan: individu atau tim.

Setelah distribusi, sistem membuat agenda untuk setiap petugas dan mengaktifkan jadwal pengingat.

### FR-07 Surat Keputusan

Data khusus minimal:

- Judul keputusan.
- Tentang.
- Menimbang.
- Mengingat.
- Memperhatikan.
- Memutuskan.
- Menetapkan.
- Diktum berurutan.
- Masa berlaku.
- Susunan tim/panitia jika ada.
- Lampiran keputusan.

Sistem menyediakan builder susunan tim dengan kolom nama, identitas, jabatan asal, dan kedudukan dalam keputusan.

### FR-08 Surat Laporan Antar-Satuan Kerja

Data khusus minimal:

- Unit pengirim dan unit tujuan.
- Periode/kategori laporan.
- Ringkasan eksekutif.
- Capaian atau hasil.
- Data pendukung.
- Kendala.
- Rekomendasi.
- Rencana tindak lanjut.
- Penanggung jawab.
- Lampiran.
- Batas respons jika diperlukan.

Unit penerima dapat menerima, memberikan catatan, meminta revisi, meneruskan, atau membuat disposisi.

### FR-09 Persetujuan berjenjang

- Alur dapat dikonfigurasi berdasarkan jenis surat, unit, nilai tertentu, dan sifat dokumen.
- Mendukung satu atau beberapa approver pada satu tahap.
- Mendukung persetujuan serial dan paralel.
- Catatan wajib saat menolak atau meminta revisi.
- Approver dapat melihat versi, lampiran, dan histori sebelum mengambil keputusan.
- Sistem merekam pengguna, waktu, alamat IP, perangkat, keputusan, dan catatan.
- Mendukung delegasi berjangka.

### FR-10 Penomoran surat

- Format dapat dikonfigurasi per instansi, unit, dan jenis surat.
- Komponen dapat mencakup nomor urut, klasifikasi, kode surat, kode unit, bulan Romawi, dan tahun.
- Nomor harus unik dalam ruang lingkup yang ditetapkan.
- Nomor final diberikan setelah persetujuan sesuai konfigurasi.
- Pembatalan nomor harus tercatat dan tidak boleh digunakan ulang tanpa kebijakan khusus.
- Sistem menyediakan buku agenda nomor surat.

Contoh: `045/ST/LPSI/UMPO/IX/2026`.

### FR-11 Penandatanganan dan verifikasi

- Sistem mencatat keputusan penandatangan.
- MVP dapat menempatkan representasi tanda tangan/stempel sesuai kebijakan internal.
- PDF final memiliki QR Code dan kode verifikasi.
- Sistem membuat hash dokumen final.
- Halaman verifikasi menampilkan metadata minimum dan status dokumen.
- Tahap lanjut menggunakan tanda tangan elektronik tersertifikasi melalui penyedia resmi.

### FR-12 Distribusi

- Surat dapat dikirim ke pengguna, jabatan, unit, atau pihak eksternal yang dicatat.
- Distribusi internal menghasilkan entri kotak masuk dan notifikasi.
- Sistem mencatat waktu dikirim, diterima, dibuka, dan diunduh sesuai kebijakan privasi.
- Surat rahasia hanya dapat dibuka oleh penerima yang ditetapkan.
- Tembusan dibedakan dari pihak yang wajib menindaklanjuti.

### FR-13 Disposisi

- Pimpinan dapat membuat disposisi kepada unit atau pegawai.
- Disposisi memuat instruksi, prioritas, tenggat, dan lampiran.
- Penerima dapat memperbarui status dan memberikan hasil tindak lanjut.
- Sistem mendukung disposisi lanjutan jika diizinkan.
- Semua rantai disposisi dapat ditelusuri.

### FR-14 Agenda dan pengingat

- Surat Tugas dan Undangan otomatis menghasilkan agenda.
- Agenda menampilkan waktu, lokasi, pihak terkait, dokumen, dan status.
- Pengingat dapat dikonfigurasi H-7, H-3, H-1, atau beberapa jam sebelumnya.
- Pengguna dapat mengatur preferensi kanal notifikasi.
- Perubahan jadwal mengirim pemberitahuan ulang.
- Pembatalan surat membatalkan atau menandai agenda terkait.

### FR-15 Laporan pelaksanaan

- Laporan terhubung dengan Surat Tugas.
- Mendukung laporan individu dan laporan tim.
- Ketua tim dapat mengompilasi kontribusi anggota.
- Form memuat realisasi waktu/lokasi, ringkasan, hasil, kendala, kesimpulan, rekomendasi, dokumentasi, dan lampiran.
- Laporan dapat diperiksa, dikembalikan untuk revisi, dan disetujui.
- Status Surat Tugas berubah menjadi selesai setelah syarat laporan terpenuhi.

### FR-16 Arsip dan pencarian

- Pencarian berdasarkan nomor, judul, isi, pihak, unit, tanggal, jenis, status, dan klasifikasi.
- Filter dapat dikombinasikan.
- Arsip dikelompokkan menurut tahun dan unit.
- Pengguna hanya melihat arsip sesuai kewenangan.
- Dokumen final dan lampiran dapat diunduh sebagai paket.
- Tahap lanjut mendukung jadwal retensi.

### FR-17 Notifikasi

- Notifikasi untuk pengajuan, persetujuan, revisi, penolakan, distribusi, disposisi, perubahan jadwal, dan tenggat laporan.
- Notifikasi memiliki status dibaca/belum dibaca.
- Pengguna dapat membuka objek terkait langsung dari notifikasi.
- Sistem menghindari pengiriman notifikasi duplikat.
- Kanal MVP: dalam aplikasi dan email.
- Kanal lanjutan: web push dan WhatsApp.

### FR-18 Dashboard

Dashboard pengguna menampilkan:

- Agenda hari ini dan mendatang.
- Surat yang membutuhkan tindakan.
- Draf dan surat yang perlu revisi.
- Penugasan aktif.
- Laporan mendekati tenggat atau terlambat.
- Notifikasi terbaru.

Dashboard pimpinan menampilkan:

- Antrean persetujuan.
- Rata-rata waktu persetujuan.
- Surat per unit dan jenis.
- Tugas aktif dan terlambat.
- Kepatuhan pengumpulan laporan.
- Status disposisi.

### FR-19 Audit log

- Mencatat login, perubahan data penting, pengajuan, persetujuan, penolakan, revisi, penomoran, penandatanganan, distribusi, unduhan dokumen rahasia, dan pencabutan.
- Log tidak dapat diubah melalui antarmuka aplikasi.
- Auditor dapat memfilter dan mengekspor log sesuai hak akses.
- Data sensitif seperti kata sandi, token mentah, dan isi rahasia tidak boleh dimasukkan ke log.

### FR-20 Pelaporan dan ekspor

- Rekap surat per jenis, unit, periode, dan status.
- Rekap kinerja persetujuan.
- Rekap penugasan dan laporan.
- Ekspor Excel/CSV dan PDF.
- Laporan mengikuti pembatasan hak akses pengguna.

---

## 14. Status Dokumen

| Status | Makna |
|---|---|
| Draft | Belum diajukan |
| Diajukan | Telah dikirim ke alur pemeriksaan |
| Dalam Pemeriksaan | Sedang diperiksa verifikator |
| Perlu Revisi | Dikembalikan kepada pembuat |
| Menunggu Persetujuan | Menunggu keputusan approver |
| Ditolak | Tidak disetujui dan alur dihentikan |
| Disetujui | Seluruh persetujuan terpenuhi |
| Menunggu Tanda Tangan | Menunggu pengesahan penandatangan |
| Ditandatangani | Telah disahkan |
| Didistribusikan | Telah dikirim kepada penerima |
| Dalam Pelaksanaan | Sedang ditindaklanjuti |
| Menunggu Laporan | Pelaksanaan selesai, laporan belum lengkap |
| Selesai | Seluruh kewajiban terpenuhi |
| Diarsipkan | Telah masuk arsip final |
| Dibatalkan | Pengajuan dihentikan sebelum final |
| Dicabut | Surat final dinyatakan tidak berlaku |

Status internal persetujuan, distribusi, dan tindak lanjut sebaiknya disimpan terpisah agar status keseluruhan tidak ambigu.

---

## 15. Aturan Bisnis

### BR-01 Kepemilikan dokumen

- Setiap dokumen wajib memiliki instansi dan unit pemilik.
- Pembuat tidak otomatis dapat melihat semua surat dalam unitnya kecuali memperoleh kewenangan.

### BR-02 Perubahan dokumen

- Draf dapat diedit oleh pembuat dan kolaborator yang diizinkan.
- Dokumen yang sedang diproses hanya dapat diedit setelah dikembalikan.
- Dokumen final tidak dapat diedit; perubahan harus menggunakan pencabutan, surat pengganti, atau addendum.

### BR-03 Persetujuan

- Pengguna tidak boleh menyetujui tahap yang bukan kewenangannya.
- Approver wajib membuka ringkasan dan dapat diwajibkan membuka lampiran tertentu sebelum menyetujui.
- Penolakan dan permintaan revisi wajib disertai alasan.
- Delegasi hanya berlaku pada rentang waktu dan lingkup yang ditentukan.

### BR-04 Nomor surat

- Nomor bersifat unik berdasarkan konfigurasi buku agenda.
- Nomor final tidak boleh diterbitkan dua kali.
- Sistem menggunakan transaksi database saat memesan nomor.
- Nomor yang batal tetap masuk histori.

### BR-05 Penerima tugas

- Penerima internal harus berasal dari data pegawai aktif, kecuali diizinkan sebagai pihak eksternal.
- Setiap penerima wajib memperoleh salinan atau akses terhadap surat final.
- Perubahan anggota setelah surat final memerlukan dokumen perubahan atau surat pengganti.

### BR-06 Laporan pelaksanaan

- Kewajiban laporan ditentukan saat Surat Tugas dibuat.
- Untuk laporan tim, surat dianggap selesai setelah laporan tim disetujui.
- Untuk laporan individu, surat dianggap selesai ketika seluruh penerima wajib telah memenuhi laporan atau memperoleh pengecualian resmi.

### BR-07 Kerahasiaan

- Surat rahasia tidak muncul pada hasil pencarian pengguna yang tidak berwenang.
- Isi notifikasi surat rahasia tidak boleh membocorkan perihal sensitif.
- Unduhan dan pembukaan dokumen rahasia dicatat.

### BR-08 Arsip

- Dokumen final tidak boleh dihapus permanen melalui operasi pengguna biasa.
- Penonaktifan pengguna/unit tidak menghapus dokumen historis.
- Semua surat historis mempertahankan nama, jabatan, unit, dan template pada saat penerbitan.

### BR-09 Waktu

- Sistem menyimpan timestamp dalam UTC dan menampilkan sesuai zona waktu instansi.
- Tenggat dan pengingat harus mempertimbangkan zona waktu pengguna/instansi.

### BR-10 Verifikasi publik

- Halaman verifikasi tidak menampilkan data pribadi berlebih.
- Surat rahasia tidak boleh memperlihatkan isi, penerima, atau lampiran pada halaman publik.
- Dokumen dicabut tetap dapat diverifikasi dengan status tidak berlaku.

---

## 16. Alur Persetujuan Contoh

### Surat Tugas standar

`Pembuat → Kepala Unit → Tata Usaha → Pimpinan/Penandatangan`

### Surat Keputusan

`Pengusul → Kepala Unit → Tata Usaha → Bagian Hukum (opsional) → Pimpinan`

### Surat Laporan Antar-Unit

`Pembuat → Kepala Unit Pengirim → Tata Usaha → Unit Tujuan`

Alur harus dikonfigurasi, bukan ditulis permanen dalam kode. Sistem wajib menyimpan salinan alur yang digunakan pada saat surat diajukan agar perubahan konfigurasi tidak merusak proses aktif.

---

## 17. Information Architecture

### 17.1 Menu pengguna

- Beranda.
- Kotak Masuk.
- Surat Saya.
- Penugasan.
- Agenda.
- Disposisi.
- Laporan.
- Arsip.
- Notifikasi.
- Profil.

### 17.2 Menu administrasi

- Instansi.
- Struktur Organisasi.
- Satuan Kerja.
- Pegawai dan Pengguna.
- Jabatan dan Pejabat.
- Role dan Hak Akses.
- Jenis Surat.
- Template Surat.
- Alur Persetujuan.
- Format Penomoran.
- Klasifikasi Arsip.
- Konfigurasi Notifikasi.
- Integrasi.
- Audit Log.
- Pengaturan Sistem.

---

## 18. Kebutuhan UI/UX Mobile-First

### 18.1 Prinsip desain

- Antarmuka dirancang mulai dari lebar layar mobile.
- Informasi penting dan tindakan berikutnya tampil lebih dahulu.
- Tabel desktop berubah menjadi kartu vertikal di mobile.
- Form panjang dibagi menjadi langkah singkat.
- Area sentuh tombol minimal 44 × 44 piksel.
- Tombol utama tetap mudah dijangkau di bagian bawah.
- Draf disimpan otomatis untuk mengurangi kehilangan data.
- Sistem memberikan umpan balik yang jelas setelah setiap tindakan.
- Warna status selalu disertai teks atau ikon, tidak bergantung pada warna saja.

### 18.2 Navigasi mobile

Navigasi bawah maksimal lima item:

1. Beranda.
2. Surat.
3. Tombol tambah.
4. Agenda.
5. Profil/Menu.

### 18.3 Pembuatan surat mobile

Form dibagi menjadi:

1. Pilih jenis surat.
2. Informasi dasar.
3. Penerima/petugas.
4. Isi dan lampiran.
5. Penandatangan dan alur.
6. Pratinjau.
7. Ajukan.

Setiap tahap menampilkan progres, validasi langsung, tombol **Simpan & Lanjut**, dan opsi **Simpan sebagai Draf**.

### 18.4 Persetujuan mobile

Halaman persetujuan memprioritaskan:

- Judul dan jenis surat.
- Unit pengusul.
- Ringkasan isi.
- Penerima/petugas.
- Jadwal dan tenggat.
- Lampiran.
- Catatan pemeriksa sebelumnya.
- Riwayat perubahan.
- Tombol Setujui, Kembalikan, dan Tolak.

Konfirmasi tambahan wajib muncul sebelum persetujuan final atau penandatanganan.

### 18.5 Dukungan perangkat

- Mobile: mulai 360 piksel.
- Tablet: mulai 768 piksel.
- Desktop: mulai 1024 piksel.
- Browser target: dua versi terbaru Chrome, Edge, Firefox, dan Safari.
- Sistem tetap dapat digunakan pada jaringan lambat dengan skeleton loading, kompresi lampiran, retry, dan pesan kegagalan yang informatif.

---

## 19. Kebutuhan Nonfungsional

### NFR-01 Kinerja

- Halaman utama ditargetkan tampil dalam kurang dari 3 detik pada koneksi seluler wajar.
- Respons API umum ditargetkan kurang dari 500 ms pada persentil ke-95, tidak termasuk pemrosesan PDF/file besar.
- Pencarian umum ditargetkan kurang dari 2 detik.
- Pembuatan PDF ditargetkan kurang dari 10 detik untuk dokumen normal.

### NFR-02 Ketersediaan

- Target uptime produksi minimal 99,5% per bulan pada fase awal.
- Sistem menyediakan health check dan pencatatan kesalahan.
- Proses notifikasi menggunakan antrean agar kegagalan email tidak menggagalkan transaksi utama.

### NFR-03 Skalabilitas

- Sistem mendukung penambahan instansi, unit, pengguna, dan jenis surat tanpa perubahan arsitektur utama.
- Penyimpanan file dipisahkan dari database.
- Proses berat seperti PDF dan notifikasi dapat dijalankan oleh worker.

### NFR-04 Usability

- Pengguna baru dapat membuat draf sederhana tanpa pelatihan teknis panjang.
- Pesan kesalahan menyebutkan masalah dan cara memperbaikinya.
- Aksi berisiko memerlukan konfirmasi.
- Sistem menyediakan empty state dan panduan kontekstual.

### NFR-05 Aksesibilitas

- Target mengikuti WCAG 2.1 level AA untuk fungsi utama.
- Mendukung navigasi keyboard.
- Kontras teks memadai.
- Input memiliki label yang dapat dibaca teknologi bantu.

### NFR-06 Kompatibilitas

- Layout tidak bergantung pada hover.
- File dapat diunggah melalui file picker maupun kamera mobile.
- PDF dapat dilihat atau diunduh sesuai kemampuan perangkat.

### NFR-07 Observability

- Log aplikasi terstruktur.
- Monitoring error, waktu respons, antrean, storage, dan kegagalan notifikasi.
- Setiap request memiliki correlation ID.

---

## 20. Keamanan dan Privasi

- Gunakan HTTPS pada seluruh lingkungan produksi.
- Kata sandi disimpan menggunakan algoritma hashing adaptif seperti Argon2id atau bcrypt.
- Semua query database menggunakan prepared statements atau ORM yang aman.
- Terapkan CSRF protection, validasi server-side, output encoding, dan Content Security Policy.
- Cookie sesi menggunakan `HttpOnly`, `Secure`, dan `SameSite` yang sesuai.
- Terapkan rate limiting pada login, reset kata sandi, verifikasi, dan API sensitif.
- Validasi tipe MIME, ekstensi, ukuran, dan isi file unggahan.
- Simpan lampiran di luar direktori publik atau menggunakan object storage privat.
- Unduhan file menggunakan otorisasi dan tautan sementara.
- Token verifikasi publik harus acak, panjang, dan tidak dapat ditebak.
- Pisahkan izin membaca metadata, isi surat, lampiran, menyetujui, dan mengunduh.
- Terapkan prinsip least privilege.
- Rahasia integrasi tidak disimpan di source code.
- Lakukan backup terenkripsi dan uji pemulihan berkala.
- Tetapkan kebijakan retensi audit log dan data pribadi.
- Lakukan security testing sebelum peluncuran.

Catatan: gambar tanda tangan yang ditempel pada PDF bukan pengganti tanda tangan elektronik tersertifikasi. Validitas hukum harus mengikuti kebijakan dan regulasi yang berlaku serta penyedia sertifikat elektronik resmi bila diperlukan.

---

## 21. Model Data Konseptual

### 21.1 Entitas inti

| Entitas | Fungsi |
|---|---|
| institutions | Data instansi |
| organization_units | Struktur satuan kerja |
| positions | Master jabatan |
| employees | Data pegawai |
| users | Akun pengguna |
| roles | Peran sistem |
| permissions | Hak akses granular |
| user_roles | Relasi pengguna dan peran beserta lingkup |
| official_assignments | Histori pejabat pada unit/jabatan |
| document_types | Jenis surat |
| document_templates | Template dan versi template |
| numbering_schemes | Konfigurasi format nomor |
| numbering_sequences | Nomor urut transaksional |
| documents | Metadata utama surat |
| document_versions | Isi setiap versi surat |
| document_parties | Pengirim, penerima, tembusan, dan pihak terkait |
| document_assignees | Penerima Surat Tugas dan perannya |
| decision_clauses | Konsideran dan diktum Surat Keputusan |
| workflow_definitions | Konfigurasi alur persetujuan |
| workflow_instances | Salinan alur pada satu dokumen |
| workflow_steps | Tahapan persetujuan aktual |
| approvals | Keputusan dan catatan approver |
| signatures | Metadata penandatanganan |
| document_files | PDF final dan lampiran |
| distributions | Riwayat distribusi |
| dispositions | Instruksi disposisi |
| agendas | Agenda hasil surat/kegiatan |
| reminders | Jadwal pengingat |
| execution_reports | Laporan pelaksanaan |
| report_contributors | Kontributor laporan tim |
| notifications | Notifikasi pengguna |
| archive_classifications | Klasifikasi arsip |
| audit_logs | Jejak aktivitas |
| delegations | Delegasi kewenangan |

### 21.2 Prinsip desain data

- Gunakan primary key internal yang tidak bergantung pada nomor surat.
- Nomor surat disimpan terpisah dan diberi unique constraint sesuai ruang lingkup.
- Gunakan soft delete untuk master data yang memiliki histori.
- Dokumen final bersifat immutable.
- Data nama/jabatan/instansi pada dokumen final disimpan sebagai snapshot.
- Lampiran menyimpan checksum untuk mendeteksi perubahan.
- Semua tabel transaksi memiliki waktu pembuatan dan pengguna pembuat.

---

## 22. API Awal

API disiapkan agar backend dapat digunakan oleh web, PWA, dan aplikasi mobile di masa depan.

### Autentikasi

- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `POST /api/v1/auth/forgot-password`
- `GET /api/v1/auth/me`

### Dokumen

- `GET /api/v1/documents`
- `POST /api/v1/documents`
- `GET /api/v1/documents/{id}`
- `PATCH /api/v1/documents/{id}`
- `POST /api/v1/documents/{id}/submit`
- `POST /api/v1/documents/{id}/cancel`
- `GET /api/v1/documents/{id}/preview`
- `GET /api/v1/documents/{id}/versions`

### Persetujuan

- `GET /api/v1/approvals/inbox`
- `POST /api/v1/approvals/{stepId}/approve`
- `POST /api/v1/approvals/{stepId}/revise`
- `POST /api/v1/approvals/{stepId}/reject`

### Distribusi dan disposisi

- `POST /api/v1/documents/{id}/distribute`
- `POST /api/v1/documents/{id}/dispositions`
- `PATCH /api/v1/dispositions/{id}/status`

### Agenda dan laporan

- `GET /api/v1/agendas`
- `GET /api/v1/assignments`
- `POST /api/v1/assignments/{id}/reports`
- `PATCH /api/v1/reports/{id}`
- `POST /api/v1/reports/{id}/submit`

### Verifikasi

- `GET /verify/{verificationCode}`

Semua endpoint internal wajib menggunakan autentikasi, otorisasi lingkup data, validasi input, pagination, dan format respons konsisten.

---

## 23. Notifikasi

### Event notifikasi utama

- Draf berhasil diajukan.
- Surat masuk ke antrean pemeriksa.
- Surat perlu revisi.
- Surat ditolak.
- Surat menunggu persetujuan.
- Surat disetujui atau ditandatangani.
- Surat didistribusikan.
- Pengguna menerima penugasan.
- Jadwal kegiatan berubah atau dibatalkan.
- Agenda mendekati waktu mulai.
- Laporan mendekati tenggat.
- Laporan terlambat.
- Laporan diterima atau perlu revisi.
- Pengguna menerima disposisi.

Notifikasi harus bersifat idempotent dan mempunyai deep link ke halaman yang relevan.

---

## 24. Pembuatan PDF

- PDF mengikuti template resmi per unit.
- Mendukung ukuran A4 dan margin konfigurabel.
- Mendukung kop, nomor, isi, tabel anggota, lampiran, tanda tangan, stempel, dan QR Code.
- Font harus ditanam atau dipastikan tersedia agar layout konsisten.
- PDF final tidak dibuat ulang dari data terbaru; file final dipertahankan sebagai bukti versi yang ditandatangani.
- Sistem menyimpan hash PDF final.
- Pratinjau diberi watermark **DRAF** sampai dokumen disahkan.

---

## 25. Pencarian dan Filter

Parameter pencarian minimum:

- Kata kunci.
- Nomor surat.
- Jenis dokumen.
- Unit pengirim.
- Unit tujuan.
- Pembuat.
- Penandatangan.
- Penerima tugas.
- Rentang tanggal.
- Status.
- Sifat/kerahasiaan.
- Klasifikasi arsip.
- Status tindak lanjut.

Filter aktif harus terlihat jelas dan dapat dihapus satu per satu. Pada mobile, filter ditampilkan melalui bottom sheet atau halaman filter khusus.

---

## 26. Dashboard dan Metrik

### Metrik operasional

- Jumlah surat dibuat per periode.
- Surat menurut jenis dan unit.
- Surat menunggu persetujuan.
- Rata-rata waktu setiap tahap.
- Persentase surat perlu revisi.
- Penugasan aktif, selesai, dan terlambat.
- Kepatuhan laporan.
- Disposisi terbuka dan lewat tenggat.

### Metrik adopsi

- Pengguna aktif harian/bulanan.
- Persentase surat internal yang diproses digital.
- Persentase persetujuan melalui mobile.
- Pengguna yang mengaktifkan notifikasi.
- Tingkat keberhasilan pengiriman notifikasi.

---

## 27. Arsitektur Teknis Rekomendasi

### Stack awal yang sesuai

- Backend: PHP 8.x dengan MVC modular atau framework yang disepakati.
- Database: MySQL 8.x.
- UI: Bootstrap 5, JavaScript modular, dan komponen yang mobile-first.
- Tabel desktop: DataTables; mobile menggunakan card view khusus.
- PDF: mPDF atau TCPDF.
- Email: PHPMailer.
- Kalender: FullCalendar untuk desktop dan list agenda untuk mobile.
- Grafik: ApexCharts atau Chart.js.
- Queue: database queue pada MVP, dapat ditingkatkan ke Redis.
- File: storage privat lokal pada awal atau object storage kompatibel S3.
- API: REST JSON berversi.
- PWA: service worker, manifest, dan strategi cache yang aman.

### Prinsip arsitektur

- Pisahkan domain dokumen, workflow, notifikasi, file, dan arsip.
- Jangan menempatkan aturan alur surat langsung di tampilan.
- Gunakan transaksi untuk penomoran, persetujuan kritis, dan finalisasi dokumen.
- Gunakan background job untuk email, PDF berat, dan pengingat.
- Pisahkan konfigurasi rahasia dari repository.
- Siapkan API sejak awal agar aplikasi Flutter dapat ditambahkan tanpa menulis ulang backend.

---

## 28. Migrasi dan Data Awal

- Siapkan template impor instansi, unit, jabatan, pegawai, dan pengguna.
- Lakukan pembersihan duplikasi identitas pegawai sebelum impor.
- Tentukan tanggal mulai penggunaan nomor surat dari sistem.
- Surat lama dapat dimasukkan sebagai arsip historis tanpa melewati workflow.
- Arsip historis harus diberi penanda **Migrasi** dan sumber data.
- Lakukan validasi sampling setelah migrasi.

---

## 29. Acceptance Criteria MVP

MVP dianggap siap uji penerimaan apabila:

1. Admin dapat membuat struktur instansi, unit, pengguna, jabatan, dan peran.
2. Pengguna dapat membuat Surat Tugas, Surat Keputusan, dan Surat Laporan melalui mobile maupun desktop.
3. Draf tersimpan dan dapat dilanjutkan pada sesi berikutnya.
4. Sistem menolak pengajuan yang belum memenuhi field wajib.
5. Dokumen dapat melewati minimal dua tingkat persetujuan.
6. Permintaan revisi menyimpan alasan dan menghasilkan versi baru.
7. Nomor final unik dan dibuat tanpa kondisi balapan.
8. Sistem menghasilkan PDF final sesuai template.
9. QR Code membuka halaman verifikasi dengan status yang benar.
10. Penerima internal memperoleh surat dan notifikasi.
11. Surat Tugas menghasilkan agenda setiap penerima.
12. Penerima dapat mengirim laporan pelaksanaan beserta lampiran dari mobile.
13. Kepala unit dapat memeriksa laporan.
14. Pencarian hanya mengembalikan data yang boleh diakses pengguna.
15. Surat rahasia terlindungi dari pengguna yang tidak berwenang.
16. Semua keputusan persetujuan dan tindakan sensitif tercatat dalam audit log.
17. Layout dapat digunakan tanpa scroll horizontal pada layar 360 piksel.
18. Pengujian keamanan kritis tidak menemukan kerentanan blocker sebelum produksi.

---

## 30. Skenario Uji Utama

### Skenario 1 — Surat Tugas berhasil

Pembuat membuat surat, memilih delapan pegawai, mengajukan, mendapatkan persetujuan, menghasilkan nomor dan PDF, lalu seluruh pegawai menerima agenda serta notifikasi.

### Skenario 2 — Surat dikembalikan

Tata usaha menemukan kesalahan jadwal, memberikan catatan, pembuat memperbaiki, sistem menyimpan versi, dan surat diajukan kembali.

### Skenario 3 — Persetujuan mobile

Pimpinan membuka notifikasi melalui telepon, membaca ringkasan dan lampiran, menyetujui surat, lalu sistem mencatat keputusan dan perangkat.

### Skenario 4 — Laporan terlambat

Sistem mengirim pengingat sebelum tenggat, menandai laporan terlambat setelah tenggat, dan menampilkan keterlambatan pada dashboard kepala unit.

### Skenario 5 — Dokumen rahasia

Pengguna yang tidak termasuk penerima tidak dapat menemukan, membuka, mengunduh, atau melihat detail surat rahasia.

### Skenario 6 — Verifikasi surat dicabut

QR Code tetap dapat dibuka, tetapi halaman menunjukkan bahwa surat telah dicabut dan tidak berlaku.

### Skenario 7 — Penomoran serentak

Dua dokumen difinalisasi bersamaan dan mendapatkan nomor berbeda tanpa duplikasi.

---

## 31. Roadmap Pengembangan

### Fase 0 — Discovery dan desain

- Validasi jenis surat dan struktur organisasi.
- Inventarisasi format kop dan nomor.
- Pemetaan alur persetujuan setiap unit.
- Wireframe desktop dan mobile.
- Prototipe pembuatan dan persetujuan surat.

### Fase 1 — Fondasi

- Autentikasi.
- Pengguna, pegawai, jabatan, dan unit.
- RBAC.
- Template dasar.
- Audit log dasar.

### Fase 2 — Dokumen inti

- Surat Tugas.
- Surat Keputusan.
- Surat Laporan.
- Versi draf.
- Lampiran dan pratinjau.

### Fase 3 — Workflow dan finalisasi

- Persetujuan berjenjang.
- Revisi dan penolakan.
- Penomoran.
- PDF final.
- QR verifikasi.
- Distribusi.

### Fase 4 — Penugasan dan laporan

- Agenda.
- Pengingat.
- Laporan individu/tim.
- Dashboard pimpinan.

### Fase 5 — Pilot dan produksi

- Migrasi data awal.
- UAT satu atau dua unit pilot.
- Perbaikan hasil pilot.
- Pelatihan.
- Peluncuran bertahap.

### Fase lanjutan

- Surat eksternal dan disposisi lanjutan.
- Tanda tangan elektronik tersertifikasi.
- Integrasi kepegawaian, kalender, web push, dan WhatsApp.
- Aplikasi Flutter.
- Retensi arsip dan analitik lanjutan.

---

## 32. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Alur setiap unit berbeda | Implementasi menjadi kompleks | Gunakan workflow configurable dan mulai dari unit pilot |
| Pengguna tetap memakai dokumen manual | Adopsi rendah | Buat proses lebih cepat, pelatihan, SOP, dan dukungan pimpinan |
| Format surat sering berubah | PDF tidak konsisten | Versioning template dan preview wajib |
| Penomoran ganda | Validitas administrasi terganggu | Unique constraint dan transaksi database |
| Tanda tangan gambar disalahgunakan | Risiko hukum/keamanan | Pembatasan akses, audit, QR, hash, dan integrasi PSrE |
| Lampiran sangat besar | Kinerja dan storage terganggu | Batas ukuran, kompresi, object storage, dan kebijakan retensi |
| Notifikasi tidak terkirim | Agenda terlewat | Queue, retry, monitoring, serta notifikasi dalam aplikasi |
| Hak akses salah konfigurasi | Kebocoran dokumen | Least privilege, uji izin otomatis, dan audit berkala |
| Mobile hanya menjadi versi desktop sempit | Pengalaman buruk | Card view, step form, sticky action, dan pengujian perangkat nyata |
| Perubahan pejabat merusak histori | Dokumen lama tidak akurat | Simpan snapshot identitas pada versi final |

---

## 33. Ketergantungan dan Keputusan yang Perlu Divalidasi

Sebelum implementasi, pemilik produk perlu memutuskan:

1. Nama resmi aplikasi.
2. Apakah sistem digunakan satu instansi atau multi-instansi.
3. Daftar jenis surat prioritas.
4. Format nomor setiap jenis surat dan unit.
5. Siapa yang berwenang memberikan nomor.
6. Alur persetujuan untuk setiap jenis surat.
7. Kebijakan delegasi pejabat.
8. Kebijakan surat rahasia.
9. Jenis laporan yang wajib setelah penugasan.
10. Batas ukuran dan jenis file lampiran.
11. Kanal notifikasi yang digunakan pada MVP.
12. Legalitas tanda tangan dan penyedia tanda tangan elektronik.
13. Masa retensi dokumen dan audit log.
14. Kebutuhan integrasi data pegawai.
15. Lokasi hosting, backup, dan disaster recovery.

---

## 34. Definition of Done

Sebuah fitur dinyatakan selesai apabila:

- Requirement dan acceptance criteria terpenuhi.
- Validasi input tersedia di client dan server.
- Hak akses diuji untuk peran yang relevan.
- Tampilan diuji pada mobile, tablet, dan desktop.
- Tidak terdapat error kritis di log.
- Audit event yang relevan tercatat.
- Dokumentasi teknis dan petunjuk penggunaan diperbarui.
- Unit test/integration test yang relevan lulus.
- Product Owner menerima hasil UAT.

---

## 35. Rekomendasi Langkah Berikutnya

1. Validasi PRD bersama pimpinan, tata usaha, kepala unit, dan calon pengguna.
2. Kumpulkan contoh asli Surat Tugas, Surat Keputusan, serta Surat Laporan.
3. Petakan format nomor dan alur persetujuan setiap unit.
4. Susun dokumen Business Rules terpisah per modul.
5. Buat sitemap serta wireframe mobile dan desktop.
6. Susun ERD dan spesifikasi database.
7. Tetapkan arsitektur backend dan kontrak API.
8. Bangun MVP untuk satu unit pilot sebelum peluncuran luas.

---

## 36. Kesimpulan

SIMANTAP dirancang sebagai sistem administrasi end-to-end. Nilai utamanya bukan sekadar membuat PDF, melainkan memastikan setiap surat memiliki pemilik, alur persetujuan, nomor yang valid, penerima yang jelas, tindak lanjut yang dapat dipantau, laporan yang terhubung, serta arsip dan jejak audit yang dapat dipertanggungjawabkan.

Pendekatan mobile-first dan API-first memungkinkan sistem digunakan segera melalui browser serta dikembangkan menjadi aplikasi Android/iOS tanpa mengganti fondasi backend.

---

## 37. Fitur Lanjutan & Peningkatan Arsitektur (v1.1 – v1.2)

### 37.1 Studio Pembuat Surat Terkustomisasi Penuh (Full-Width Studio Builder)
- **Tampilan Studio Layar Penuh:** Memanfaatkan layout 2-kolom lebar (*split pane*) yang memisahkan form konfigurasi detail di panel kiri dan kanvas simulasi A4 (*live preview*) di panel kanan.
- **Kustomisasi Header / KOP:**
  - Pengunggahan logo kiri & kanan instansi dengan pratinjau instan.
  - Teks header 3 tingkat (Instansi, Unit Kerja, Sub-unit).
  - Informasi kontak dan opsi garis pembatas kop (Ganda tebal-tipis, Tebal tunggal, Tipis, Tanpa garis).
- **Pengaturan Nomor & Perihal Fleksibel:** Pilihan sistem penomoran otomatis berdasarkan pola klasifikasi atau input manual untuk penyesuaian kebutuhan khusus.
- **Penyusun Konten (Rich Content Builder):** Dukungan formatting teks (bold, italic, underline, list, perataan teks, indentasi) serta pilihan template salam pembuka dan penutup instan.
- **Tanda Tangan & Pengesahan Dinamis:**
  - Unggahan tanda tangan digital pejabat.
  - Unggahan stempel/cap basah dengan kontrol transparansi opasitas dan posisi melayang (*floating stamp*).
  - Manajemen tembusan dinamis (*dynamic add/remove rows*).
- **Kontrol Kanvas A4:** Dukungan pembesaran (*zoom controls*: Fit, 75%, 100%, 125%) untuk akurasi cetak dokumen.

### 37.2 Panel Notifikasi Geser Sisi Kanan (Off-Canvas Right Drawer)
- **Desain Laci Interaktif:** Notifikasi muncul dari sisi kanan layar (*slide-in drawer*) saat ikon lonceng ditekan tanpa mengalihkan halaman kerja saat ini.
- **Segmentasi Tab Notifikasi:** Filter kategori *Semua*, *Persetujuan*, dan *Penugasan*.
- **Pemberitahuan Audio Halus (*Chime*):** Memberikan sinyal audio saat interaksi notifikasi baru.
- **Tandai Sudah Dibaca Real-Time:** Sinkronisasi visual status belum dibaca secara instan.

### 37.3 Arsitektur Penyimpanan File Fisik (File System Offloading)
- **Penyimpanan Gambar Terpisah:** Seluruh aset gambar (logo, tanda tangan, stempel) dipisahkan dari payload SQL dan disimpan ke direktori fisik terproteksi (`assets/uploads/letters/`).
- **Pencegahan Error Paket Database:** Mencegah terjadinya error `2006 MySQL server has gone away` akibat payload base64 gambar berukuran besar.
- **Integritas & Keamanan Berkas:** Validasi MIME type berkas, pembatasan ukuran berkas maksimal, dan pengacakan nama berkas berbasis *cryptographic hash*.

### 37.4 Skema & Tata Kelola Pemversian Aplikasi (Semantic Versioning Governance)
Untuk menjaga ketertiban rilis, SIMANTAP mewajibkan penerapan **Semantic Versioning (`MAJOR.MINOR.PATCH`)**:
1. **MAJOR (`X.0.0`):** Dinaikkan saat terjadi perombakan basis data besar, restrukturisasi arsitektur, atau perubahan alur bisnis yang tidak kompatibel dengan versi sebelumnya (*breaking changes*).
2. **MINOR (`1.X.0`):** Dinaikkan saat penambahan modul baru, fitur baru (seperti Studio Editor atau Off-Canvas Drawer), atau peningkatan UI/UX signifikan yang tetap kompatibel ke belakang.
3. **PATCH (`1.1.X`):** Dinaikkan saat perbaikan *bug*, penyesuaian CSP, patch keamanan, atau perbaikan performa kueri.
4. **Distribusi Versi Terintegrasi:** Nilai versi wajib didefinisikan pada `model/config/config.app.php` (`APP_VERSION`), didokumentasikan di `CHANGELOG.md`, ditampilkan pada `view/public/layouts/footer.php`, serta disajikan secara visual pada halaman `view/public/pages/changelog.php`.

---

**Akhir Dokumen — PRD SIMANTAP Versi 1.2**
