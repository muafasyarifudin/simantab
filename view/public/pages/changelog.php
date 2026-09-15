<?php
declare(strict_types=1);

$releases = [
    [
        'version' => '1.2.1',
        'type' => 'patch',
        'is_latest' => true,
        'date' => '15 September 2026',
        'badge' => 'Rilis Terbaru',
        'badge_theme' => 'emerald',
        'title' => 'Kustomisasi Ukuran Seluruh Elemen Surat & Sistem Auto-Save Lokal Anti-Lag',
        'description' => 'Penambahan fitur kontrol ukuran font, margin, jarak spasi baris, dan dimensi untuk seluruh komponen naskah dinas, serta sistem Auto-Save lokal cerdas tanpa membebani server.',
        'items' => [
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-font-size-2',
                'theme' => 'blue',
                'text' => '<strong>Kustomisasi Penuh Ukuran Semua Elemen:</strong> Penyesuaian ukuran font untuk KOP (3 tingkatan), metadata dokumen, tanggal, penerima (Yth.), salam pembuka/penutup, isi paragraf, jabatan & nama penandatangan, NIP, tembusan, dan catatan kaki.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-line-height',
                'theme' => 'blue',
                'text' => '<strong>Kontrol Spasi, Margin & Dimensi A4:</strong> Pengaturan margin kertas A4 (Compact, Normal, Spacious), jarak baris (Line Height 1.2 - 2.0), spasi paragraf, tinggi ruang tanda tangan (45-120px), dimensi logo, serta diameter dan opasitas stempel dinas.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-save-3-fill',
                'theme' => 'emerald',
                'text' => '<strong>Auto-Save Lokal Tanpa Beban Server:</strong> Penyimpanan draf otomatis di background menggunakan <code>localStorage</code> peramban dengan debounce 280ms (0 HTTP overhead). Dilengkapi banner pemulihan draf otomatis (Draft Recovery) dan indikator status.'
            ],
        ]
    ],
    [
        'version' => '1.2.0',
        'type' => 'minor',
        'is_latest' => false,
        'date' => '15 September 2026',
        'badge' => 'Peningkatan UI',
        'badge_theme' => 'blue',
        'title' => 'Panel Notifikasi Geser Sisi Kanan & Sinkronisasi Badge Real-Time',
        'description' => 'Peningkatan interaktivitas notifikasi dengan model off-canvas drawer dari sisi kanan layar, sinkronisasi data riil pada badge navigasi sidebar, dan peluncuran portal Changelog & Versioning terpadu.',
        'items' => [
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-add-circle-fill',
                'theme' => 'blue',
                'text' => '<strong>Panel Notifikasi Off-Canvas (Slide-in Drawer):</strong> Panel notifikasi interaktif yang meluncur dari kanan layar saat tombol lonceng ditekan, dilengkapi 3 filter tab (Semua, Persetujuan, Tugas), kontrol status baca instan, dan efek suara audio chime.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-add-circle-fill',
                'theme' => 'blue',
                'text' => '<strong>Portal Catatan Rilis & Changelog Terpadu:</strong> Halaman khusus (?page=changelog) yang menyajikan seluruh riwayat pembaruan sistem dan kebijakan pemversian Semantic Versioning (SemVer).'
            ],
            [
                'type' => 'changed',
                'label' => 'PERUBAHAN',
                'icon' => 'ri-refresh-line',
                'theme' => 'amber',
                'text' => '<strong>Sinkronisasi Badge Sidebar Real-Time:</strong> Mengganti angka statis dengan query data langsung dari database untuk notifikasi surat, persetujuan tertunda, penugasan aktif, dan laporan belum diserahkan.'
            ],
        ]
    ],
    [
        'version' => '1.1.2',
        'type' => 'patch',
        'is_latest' => false,
        'date' => '15 September 2026',
        'badge' => 'Optimasi Penyimpanan',
        'badge_theme' => 'teal',
        'title' => 'Arsitektur File System Storage & Penanganan MySQL Packet Limit',
        'description' => 'Pemisahan penyimpanan fisik file logo, tanda tangan, dan cap stempel surat ke folder disk server untuk menyelesaikan error MySQL 2006 (Server has gone away) akibat payload data base64 berlebih.',
        'items' => [
            [
                'type' => 'fixed',
                'label' => 'PERBAIKAN',
                'icon' => 'ri-bug-fill',
                'theme' => 'rose',
                'text' => '<strong>Penyelesaian Error MySQL 2006:</strong> Menangani error <code>SQLSTATE[HY000]: 2006 MySQL server has gone away</code> saat pengguna menyimpan draf surat dengan logo instansi dan tanda tangan beresolusi tinggi.'
            ],
            [
                'type' => 'changed',
                'label' => 'PERUBAHAN',
                'icon' => 'ri-hard-drive-2-line',
                'theme' => 'amber',
                'text' => '<strong>Penyimpanan Fisik Gambar Surat:</strong> File gambar diunggah ke direktori server <code>assets/uploads/letters/</code> dengan hash kriptografi unik, sehingga database hanya menyimpan metadata path relatif yang sangat ringan.'
            ],
            [
                'type' => 'security',
                'label' => 'KEAMANAN',
                'icon' => 'ri-shield-check-fill',
                'theme' => 'indigo',
                'text' => '<strong>Validasi MIME Type Ketat:</strong> Perlindungan berkas unggahan dengan validasi tipe gambar (PNG/JPEG/WEBP) dan sanitasi nama berkas guna mencegah celah keamanan upload.'
            ]
        ]
    ],
    [
        'version' => '1.1.1',
        'type' => 'patch',
        'is_latest' => false,
        'date' => '15 September 2026',
        'badge' => 'Patch Interaksi CSP',
        'badge_theme' => 'rose',
        'title' => 'Kepatuhan Content Security Policy (CSP) & Pemisahan Modul JavaScript',
        'description' => 'Restrukturisasi skrip JavaScript Studio Pembuat Surat ke berkas modul eksternal agar sepenuhnya mematuhi standar keamanan CSP tanpa memblokir fungsi klik tombol dan interaksi formulir.',
        'items' => [
            [
                'type' => 'fixed',
                'label' => 'PERBAIKAN',
                'icon' => 'ri-cursor-fill',
                'theme' => 'rose',
                'text' => '<strong>Perbaikan Hambatan Klik Tab & Tombol:</strong> Mengatasi masalah tombol tab konfigurasi, tombol upload, dan formatting bar yang tidak bisa diklik akibat pemblokiran skrip inline oleh browser.'
            ],
            [
                'type' => 'changed',
                'label' => 'PERUBAHAN',
                'icon' => 'ri-file-code-line',
                'theme' => 'amber',
                'text' => '<strong>Modul JavaScript Terpisah:</strong> Seluruh engine editor dipindahkan ke berkas <code>assets/js/letter-builder.js</code> untuk arsitektur yang lebih bersih dan modular.'
            ],
            [
                'type' => 'security',
                'label' => 'KEAMANAN',
                'icon' => 'ri-lock-2-line',
                'theme' => 'indigo',
                'text' => '<strong>Penyesuaian Header Keamanan .htaccess:</strong> Mengonfigurasi rule Content Security Policy untuk mengizinkan skrip lokal dan font resmi tanpa membuka celah XSS.'
            ]
        ]
    ],
    [
        'version' => '1.1.0',
        'type' => 'minor',
        'is_latest' => false,
        'date' => '15 September 2026',
        'badge' => 'Fitur Utama',
        'badge_theme' => 'blue',
        'title' => 'Studio Pembuat Surat Layar Penuh (Full-Width Letter Studio)',
        'description' => 'Peluncuran modul Studio Pembuat Surat canggih dengan layout 2-kolom layar penuh, kustomisasi menyeluruh (KOP, logo, pembatas garis, penomoran, isi format, tanda tangan digital, cap stempel melayang), serta simulasi kanvas A4 real-time.',
        'items' => [
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-layout-masonry-line',
                'theme' => 'blue',
                'text' => '<strong>Layout Studio Layar Penuh 2-Kolom:</strong> Panel konfigurasi 4 tab di sebelah kiri dan lembar simulasi kertas A4 di sebelah kanan dengan two-way live preview tanpa reload.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-brush-line',
                'theme' => 'blue',
                'text' => '<strong>Kustomisasi KOP Lengkap:</strong> Upload logo instansi (kiri & kanan), teks 3 tingkat (Instansi, Lembaga, Sub-unit), detail kontak, dan 4 pilihan gaya garis pembatas KOP (Ganda tebal-tipis, Tunggal, Tipis, Tanpa garis).'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-quill-pen-line',
                'theme' => 'blue',
                'text' => '<strong>Pengesahan & Cap Basah Melayang:</strong> Upload tanda tangan digital serta stempel basah dengan kontrol transparansi opasitas dan posisi melayang bebas di atas dokumen.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-zoom-in-line',
                'theme' => 'blue',
                'text' => '<strong>Kontrol Kanvas Zoom:</strong> Pilihan pembesaran lembar kerja A4 (Fit to Width, 75%, 100%, 125%) untuk ketepatan tampilan cetak.'
            ]
        ]
    ],
    [
        'version' => '1.0.0',
        'type' => 'major',
        'is_latest' => false,
        'date' => '1 September 2026',
        'badge' => 'Rilis Perdana MVP',
        'badge_theme' => 'violet',
        'title' => 'Peluncuran Perdana SIMANTAP (Sistem Terpadu Surat & Penugasan)',
        'description' => 'Fondasi awal sistem tata kelola persuratan dinas, surat tugas, surat keputusan, workflow persetujuan berjenjang, pelaporan penugasan, verifikasi publik berbasis QR Code, dan dashboard operasional.',
        'items' => [
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-file-shield-line',
                'theme' => 'blue',
                'text' => '<strong>Manajemen Dokumen Resmi:</strong> Modul pembuatan dan tata naskah untuk Surat Tugas, Surat Keputusan (SK), dan Surat Laporan Antar-Satuan Kerja.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-flow-chart',
                'theme' => 'blue',
                'text' => '<strong>Alur Persetujuan Berjenjang:</strong> Multi-tier approval workflow dengan catatan revisi, riwayat penolakan, dan delegasi wewenang.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-qr-code-line',
                'theme' => 'blue',
                'text' => '<strong>Verifikasi Publik Dokumen:</strong> Lembar verifikasi keaslian surat berbasis QR Code dan hash integritas dokumen.'
            ],
            [
                'type' => 'added',
                'label' => 'FITUR BARU',
                'icon' => 'ri-shield-user-line',
                'theme' => 'blue',
                'text' => '<strong>Keamanan & Audit Terpadu:</strong> Autentikasi sesi aman, Role-Based Access Control (RBAC), Audit Log aktivitas, dan dukungan Progressive Web App (PWA).'
            ]
        ]
    ]
];
?>

<style>
/* ==========================================================================
   CHANGELOG CUSTOM STYLES (PREMIUM MODERN TIMELINE)
   ========================================================================== */
.cl-container {
    width: 100%;
    margin: 0;
}

/* Header & Banner */
.cl-hero {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 18px;
    padding: 32px 36px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
    box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.3);
}
.cl-hero::after {
    content: "";
    position: absolute;
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(86, 100, 217, 0.25) 0%, rgba(86, 100, 217, 0) 70%);
    top: -140px;
    right: -100px;
    border-radius: 50%;
    pointer-events: none;
}
.cl-hero-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 14px;
    position: relative;
    z-index: 2;
}
.cl-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #93c5fd;
    background: rgba(147, 197, 253, 0.12);
    padding: 5px 12px;
    border-radius: 20px;
    border: 1px solid rgba(147, 197, 253, 0.2);
}
.cl-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #10b981;
    color: #ffffff;
    font-size: 12.5px;
    font-weight: 800;
    padding: 7px 16px;
    border-radius: 30px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
    letter-spacing: 0.3px;
}
.cl-hero h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 8px;
    color: #ffffff;
    letter-spacing: -0.5px;
    position: relative;
    z-index: 2;
}
.cl-hero h1 span {
    color: #818cf8;
}
.cl-hero p {
    color: #94a3b8;
    font-size: 13.5px;
    line-height: 1.6;
    margin: 0;
    max-width: 780px;
    position: relative;
    z-index: 2;
}

/* Stat Highlights */
.cl-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}
.cl-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.cl-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(32, 44, 64, 0.08);
}
.cl-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
.cl-stat-icon.emerald { background: #d1fae5; color: #059669; }
.cl-stat-icon.blue    { background: #dbeafe; color: #2563eb; }
.cl-stat-icon.purple  { background: #ede9fe; color: #7c3aed; }
.cl-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--muted);
    display: block;
    margin-bottom: 2px;
}
.cl-stat-value {
    font-size: 17px;
    font-weight: 800;
    color: var(--ink);
    display: block;
}
.cl-stat-sub {
    font-size: 11px;
    color: var(--muted);
    display: block;
    margin-top: 1px;
}

/* Timeline Stream */
.cl-timeline {
    position: relative;
    padding-left: 36px;
    margin-bottom: 36px;
}
.cl-timeline::before {
    content: "";
    position: absolute;
    left: 14px;
    top: 24px;
    bottom: 24px;
    width: 3px;
    background: linear-gradient(to bottom, var(--primary) 0%, rgba(86, 100, 217, 0.15) 100%);
    border-radius: 3px;
}

/* Timeline Release Card */
.cl-release-item {
    position: relative;
    margin-bottom: 32px;
}
.cl-release-item:last-child {
    margin-bottom: 0;
}

/* Marker Dot */
.cl-marker {
    position: absolute;
    left: -36px;
    top: 22px;
    width: 31px;
    height: 31px;
    border-radius: 50%;
    background: var(--surface);
    border: 3px solid var(--primary);
    display: grid;
    place-items: center;
    font-size: 13px;
    color: var(--primary);
    z-index: 3;
    box-shadow: 0 0 0 5px var(--ground);
    transition: transform 0.2s ease;
}
.cl-release-item.is-latest .cl-marker {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.2), 0 0 16px rgba(16, 185, 129, 0.4);
    animation: clPulse 2.5s infinite;
}
@keyframes clPulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
    70% { box-shadow: 0 0 0 9px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Card Container */
.cl-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px 28px;
    box-shadow: var(--shadow);
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.cl-card:hover {
    border-color: rgba(86, 100, 217, 0.4);
    box-shadow: 0 8px 30px rgba(32, 44, 64, 0.08);
}
.cl-release-item.is-latest .cl-card {
    border-top: 4px solid #10b981;
}

/* Card Header */
.cl-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 16px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
}
.cl-version-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-family: var(--font-mono, monospace);
    font-size: 16px;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 8px;
}
.cl-release-item.is-latest .cl-version-pill {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0;
}
.cl-status-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 4px 10px;
    border-radius: 20px;
}
.cl-status-tag.emerald { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.cl-status-tag.teal    { background: #ccfbf1; color: #115e59; border: 1px solid #99f6e4; }
.cl-status-tag.blue    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.cl-status-tag.rose    { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
.cl-status-tag.violet  { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }

.cl-date-text {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.cl-git-tag {
    font-family: var(--font-mono, monospace);
    font-size: 11px;
    color: var(--muted);
    background: var(--ground);
    border: 1px solid var(--border);
    padding: 3px 8px;
    border-radius: 6px;
}

.cl-card-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--ink);
    margin: 8px 0 6px;
    line-height: 1.35;
}
.cl-card-desc {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
    margin: 0 0 20px;
}

/* Changes Stream */
.cl-items-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.cl-item-row {
    background: var(--ground);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: background 0.15s ease;
}
.cl-item-row:hover {
    background: color-mix(in srgb, var(--surface) 50%, var(--ground));
}

/* High Contrast Pill Badges */
.cl-pill {
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    min-width: 108px;
    justify-content: center;
    flex-shrink: 0;
}
.cl-pill.blue   { background: #2563eb; color: #ffffff; box-shadow: 0 2px 6px rgba(37,99,235,0.25); }
.cl-pill.rose   { background: #e11d48; color: #ffffff; box-shadow: 0 2px 6px rgba(225,29,72,0.25); }
.cl-pill.amber  { background: #d97706; color: #ffffff; box-shadow: 0 2px 6px rgba(217,119,6,0.25); }
.cl-pill.indigo { background: #4f46e5; color: #ffffff; box-shadow: 0 2px 6px rgba(79,70,229,0.25); }

.cl-item-text {
    font-size: 13px;
    color: var(--ink);
    line-height: 1.55;
    flex: 1;
}
.cl-item-text strong {
    color: var(--ink);
    font-weight: 700;
}
.cl-item-text code {
    font-family: var(--font-mono, monospace);
    font-size: 11.5px;
    background: rgba(86, 100, 217, 0.1);
    color: var(--primary);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
}

/* SemVer Guideline Box */
.cl-policy-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow);
}
.cl-policy-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.cl-policy-icon {
    width: 40px;
    height: 40px;
    background: rgba(86, 100, 217, 0.12);
    color: var(--primary);
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 20px;
}
.cl-policy-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--ink);
    margin: 0;
}
.cl-policy-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-top: 18px;
}
.cl-policy-card {
    background: var(--ground);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px;
}
.cl-policy-tag {
    font-size: 12px;
    font-weight: 800;
    display: block;
    margin-bottom: 6px;
}
.cl-policy-tag.violet { color: #7c3aed; }
.cl-policy-tag.blue   { color: #2563eb; }
.cl-policy-tag.teal   { color: #059669; }
.cl-policy-card p {
    font-size: 11.5px;
    color: var(--muted);
    line-height: 1.5;
    margin: 0;
}

@media(max-width: 900px) {
    .cl-stats { grid-template-columns: 1fr; }
    .cl-policy-grid { grid-template-columns: 1fr; }
    .cl-card-head { flex-direction: column; align-items: flex-start; }
    .cl-item-row { flex-direction: column; gap: 8px; }
    .cl-pill { min-width: auto; align-self: flex-start; }
}
</style>

<div class="page-shell">
    <div class="cl-container">
        <!-- Hero Header -->
        <div class="cl-hero">
            <div class="cl-hero-top">
                <span class="cl-hero-eyebrow">
                    <i class="ri-shield-star-line"></i> SISTEM & CATATAN RILIS RESMI
                </span>
                <span class="cl-hero-badge">
                    <i class="ri-checkbox-circle-fill"></i> Versi Saat Ini: v<?= APP_VERSION ?>
                </span>
            </div>
            <h1>Catatan Rilis (Changelog)<span>.</span></h1>
            <p>Dokumentasi resmi seluruh pembaruan, penambahan fitur, perbaikan bug, peningkatan arsitektur, dan tata kelola pemversian aplikasi SIMANTAP.</p>
        </div>

        <!-- Metric Stat Cards -->
        <div class="cl-stats">
            <div class="cl-stat-card">
                <div class="cl-stat-icon emerald">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <div>
                    <span class="cl-stat-label">Versi Terpasang</span>
                    <strong class="cl-stat-value">v<?= APP_VERSION ?></strong>
                    <span class="cl-stat-sub">Rilis Produksi Aktif</span>
                </div>
            </div>

            <div class="cl-stat-card">
                <div class="cl-stat-icon blue">
                    <i class="ri-git-branch-line"></i>
                </div>
                <div>
                    <span class="cl-stat-label">Standar Rilis</span>
                    <strong class="cl-stat-value">SemVer 2.0.0</strong>
                    <span class="cl-stat-sub">MAJOR . MINOR . PATCH</span>
                </div>
            </div>

            <div class="cl-stat-card">
                <div class="cl-stat-icon purple">
                    <i class="ri-history-line"></i>
                </div>
                <div>
                    <span class="cl-stat-label">Riwayat Iterasi</span>
                    <strong class="cl-stat-value"><?= count($releases) ?> Catatan Rilis</strong>
                    <span class="cl-stat-sub">Dari v1.0.0 s.d. v<?= APP_VERSION ?></span>
                </div>
            </div>
        </div>

        <!-- Vertical Timeline Stream -->
        <div class="cl-timeline">
            <?php foreach ($releases as $rel): ?>
                <div class="cl-release-item <?= $rel['is_latest'] ? 'is-latest' : '' ?>">
                    <div class="cl-marker">
                        <i class="<?= $rel['is_latest'] ? 'ri-star-fill' : 'ri-git-commit-line' ?>"></i>
                    </div>

                    <div class="cl-card">
                        <!-- Card Header -->
                        <div class="cl-card-head">
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                <span class="cl-version-pill">
                                    <i class="ri-price-tag-3-fill"></i> v<?= e($rel['version']) ?>
                                </span>
                                <span class="cl-status-tag <?= $rel['badge_theme'] ?>">
                                    <?= e($rel['badge']) ?>
                                </span>
                                <span class="cl-date-text">
                                    <i class="ri-calendar-event-line"></i> <?= e($rel['date']) ?>
                                </span>
                            </div>
                            <div>
                                <span class="cl-git-tag">
                                    tag: release-v<?= e($rel['version']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <h2 class="cl-card-title"><?= e($rel['title']) ?></h2>
                        <p class="cl-card-desc"><?= e($rel['description']) ?></p>

                        <!-- Items Breakdown -->
                        <div class="cl-items-list">
                            <?php foreach ($rel['items'] as $item): ?>
                                <div class="cl-item-row">
                                    <span class="cl-pill <?= $item['theme'] ?>">
                                        <i class="<?= $item['icon'] ?>"></i> <?= e($item['label']) ?>
                                    </span>
                                    <div class="cl-item-text">
                                        <?= $item['text'] ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Semantic Versioning Guidelines -->
        <div class="cl-policy-box">
            <div class="cl-policy-head">
                <div class="cl-policy-icon">
                    <i class="ri-book-open-line"></i>
                </div>
                <div>
                    <h3 class="cl-policy-title">Tata Kelola & Panduan Pemversian (Semantic Versioning)</h3>
                    <p style="font-size:12px; color:var(--muted); margin:2px 0 0;">
                        Setiap rilis SIMANTAP mematuhi format <code>MAJOR.MINOR.PATCH</code> untuk menjamin transparansi perubahan bagi pengguna dan pengembang.
                    </p>
                </div>
            </div>

            <div class="cl-policy-grid">
                <div class="cl-policy-card">
                    <span class="cl-policy-tag violet">
                        <i class="ri-number-1"></i> Versi MAJOR (X.0.0)
                    </span>
                    <p>Dinaikkan saat terjadi perubahan arsitektural besar, perombakan basis data, atau perubahan alur bisnis yang tidak kompatibel dengan versi sebelumnya (<em>breaking changes</em>).</p>
                </div>

                <div class="cl-policy-card">
                    <span class="cl-policy-tag blue">
                        <i class="ri-number-2"></i> Versi MINOR (0.X.0)
                    </span>
                    <p>Dinaikkan saat penambahan modul baru atau fitur besar (seperti Full-Width Studio Builder & Notification Drawer) yang tetap kompatibel dengan fungsi terdahulu.</p>
                </div>

                <div class="cl-policy-card">
                    <span class="cl-policy-tag teal">
                        <i class="ri-number-3"></i> Versi PATCH (0.0.X)
                    </span>
                    <p>Dinaikkan saat perbaikan bug, penyesuaian aturan CSP, optimasi penyimpanan file, patch keamanan, atau perbaikan visual antarmuka.</p>
                </div>
            </div>
        </div>
    </div>
</div>
