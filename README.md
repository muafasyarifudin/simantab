# SIMANTAP

MVP antarmuka Sistem Manajemen Surat dan Penugasan Terpadu berdasarkan `PRD.md`. Struktur mengikuti pola SIBAIK (`controller`, `model`, `view`, `assets`, `content`) dan visual mengikuti design system CANTIK/BEAT UI.

## Menjalankan

1. Pastikan Apache XAMPP aktif.
2. Buka `http://localhost/simantab/`.
3. Aplikasi otomatis memakai akun demo. Halaman login tersedia pada `?page=login` dan menerima sembarang isian untuk mode demo.

## Struktur

- `controller/route` — pemetaan halaman.
- `model/config` — konfigurasi aplikasi.
- `model/data` — repository data demo (siap diganti query MySQL).
- `model/function` — helper tampilan/domain.
- `view/public/layouts` — shell sidebar, topbar, footer.
- `view/public/pages` — modul aplikasi.
- `view/private/page` — autentikasi.
- `assets` — CSS, JavaScript, ikon, dan font lokal.
- `database/schema.sql` — skema lengkap database `db_simantab` (52 tabel).
- `database/seed.sql` — data awal instansi, unit, admin, RBAC, jenis surat, klasifikasi, dan penomoran.

### Database demo

Database `db_simantab` telah disiapkan untuk MariaDB/MySQL. Akun awal pada seed: username `admin`, kata sandi `Admin@123` dan wajib diganti saat login pertama.

## Catatan fase berikutnya

Backend menggunakan PDO dan database `db_simantab`. Implementasi mencakup autentikasi password hash dan throttling, RBAC per unit, CRUD/versioning dokumen, workflow, penomoran transaksional, PDF/QR/hash, file privat, distribusi, disposisi, agenda, laporan, notifikasi, audit, dan REST API.

Jalankan worker pengingat/email secara periodik melalui Task Scheduler: `C:\xampp\php\php.exe C:\xampp\htdocs\simantab\worker.php`. Konfigurasikan mail transport PHP/XAMPP sebelum memakai email produksi.

Pengujian: `php tests/integration.php` dan `php tests/security.php`. Dokumentasi endpoint tersedia di `API.md`.
