<?php
declare(strict_types=1);

$activeTab = $_GET['tab'] ?? ($_GET['page'] === 'numbering' ? 'numbering' : 'templates');

// 1. Data Template Dokumen
$templates = [
    ['ri-briefcase-4-line', 'Surat Tugas', 'ST', 'v2.1', '12 Sep 2026', 'Aktif', 'blue', 'Template penugasan resmi untuk satu atau kelompok pegawai dengan rincian jadwal, lokasi, dan laporan.'],
    ['ri-government-line', 'Surat Keputusan (SK)', 'SK', 'v1.8', '08 Sep 2026', 'Aktif', 'violet', 'Dokumen ketetapan pimpinan dengan konsideran Menimbang, Mengingat, Memutuskan, dan susunan diktum.'],
    ['ri-file-chart-line', 'Surat Laporan', 'LAP', 'v1.3', '02 Sep 2026', 'Aktif', 'teal', 'Format penyampaian capaian periodik, laporan pertanggungjawaban, dan koordinasi antar-satuan kerja.'],
    ['ri-mail-open-line', 'Nota Dinas', 'ND', 'v1.1', '28 Agu 2026', 'Aktif', 'amber', 'Komunikasi dinas internal antar-pejabat struktural dalam lingkup instansi yang ringkas dan terarah.'],
    ['ri-calendar-event-line', 'Surat Undangan', 'UND', 'v1.0', '20 Agu 2026', 'Aktif', 'indigo', 'Format undangan rapat resmi, seminar, workshop, dan agenda institusi dengan jadwal terintegrasi.'],
    ['ri-file-list-3-line', 'Berita Acara', 'BA', 'v1.0', '15 Agu 2026', 'Aktif', 'rose', 'Format berita acara serah terima jabatan, verifikasi barang, atau penetapan hasil musyawarah formal.'],
];

// 2. Data Skema Penomoran
$schemes = [
    ['SK-01', 'Surat Tugas Instansi', 'ST', '{SEQ:3}/ST/{UNIT}/UMPO/{ROMAN}/{YEAR}', 'LPSI / Semua Unit', '048', '048/ST/LPSI/UMPO/IX/2026', 'Aktif'],
    ['SK-02', 'Surat Keputusan Rektor', 'SK', '{SEQ:3}/SK/R/{UNIT}/{YEAR}', 'Rektorat / Pimpinan', '019', '019/SK/R/UMPO/2026', 'Aktif'],
    ['SK-03', 'Surat Laporan Antar-Unit', 'LAP', '{SEQ:3}/LAP/{UNIT}/{ROMAN}/{YEAR}', 'Semua Satuan Kerja', '012', '012/LAP/LPPM/IX/2026', 'Aktif'],
    ['SK-04', 'Nota Dinas Internal', 'ND', '{SEQ:3}/ND/{UNIT}/{ROMAN}/{YEAR}', 'Semua Satuan Kerja', '034', '034/ND/BAAK/IX/2026', 'Aktif'],
    ['SK-05', 'Surat Undangan Dinas', 'UND', '{SEQ:3}/UND/{UNIT}/{ROMAN}/{YEAR}', 'Semua Satuan Kerja', '027', '027/UND/FTIK/IX/2026', 'Aktif'],
    ['SK-06', 'Berita Acara Resmi', 'BA', '{SEQ:3}/BA/{UNIT}/{ROMAN}/{YEAR}', 'Biro Umum & Keuangan', '005', '005/BA/BUK/IX/2026', 'Aktif'],
];

// 3. Data Alur Persetujuan (Workflows)
$workflows = [
    [
        'id' => 'WF-01',
        'name' => 'Alur Persetujuan Surat Tugas Standar',
        'type' => 'Surat Tugas (ST)',
        'steps' => ['Pembuat Draf', 'Kepala Unit', 'Verifikator TU', 'Penandatangan'],
        'sla' => '1 x 24 Jam',
        'status' => 'Aktif'
    ],
    [
        'id' => 'WF-02',
        'name' => 'Alur Surat Keputusan (SK) Pimpinan',
        'type' => 'Surat Keputusan (SK)',
        'steps' => ['Pengusul', 'Kepala Biro', 'Tata Usaha', 'Bagian Hukum', 'Rektor'],
        'sla' => '3 x 24 Jam',
        'status' => 'Aktif'
    ],
    [
        'id' => 'WF-03',
        'name' => 'Alur Laporan Antar-Satuan Kerja',
        'type' => 'Surat Laporan (LAP)',
        'steps' => ['Penyusun Laporan', 'Kepala Unit Pengirim', 'Verifikasi TU', 'Unit Penerima'],
        'sla' => '2 x 24 Jam',
        'status' => 'Aktif'
    ],
    [
        'id' => 'WF-04',
        'name' => 'Alur Nota Dinas Singkat',
        'type' => 'Nota Dinas (ND)',
        'steps' => ['Pengirim', 'Tata Usaha', 'Pejabat Tujuan'],
        'sla' => '12 Jam',
        'status' => 'Aktif'
    ]
];
?>

<div class="page-shell">
    <header class="page-header">
        <div>
            <p class="eyebrow">ADMINISTRASI PERSURATAN</p>
            <h1>Template & Penomoran Dokumen<span>.</span></h1>
            <p>Atur tata letak dokumen, pola kode penomoran otomatis, dan konfigurasi alur persetujuan berjenjang.</p>
        </div>
        <button class="primary-button" id="mainPageActionBtn"
            data-templates-text="<i class='ri-add-line'></i> Template Baru"
            data-numbering-text="<i class='ri-hashtag'></i> Skema Nomor Baru"
            data-workflows-text="<i class='ri-flow-chart'></i> Alur Kerja Baru">
            <i class="ri-add-line"></i> Template Baru
        </button>
    </header>

    <!-- Interactive Navigation Tabs -->
    <div class="summary-tabs">
        <button type="button" class="<?= $activeTab === 'templates' ? 'active' : '' ?>" onclick="window.switchSummaryTab('templates', this)">
            <i class="ri-layout-4-line"></i> Template Surat <span><?= count($templates) ?></span>
        </button>
        <button type="button" class="<?= $activeTab === 'numbering' ? 'active' : '' ?>" onclick="window.switchSummaryTab('numbering', this)">
            <i class="ri-hashtag"></i> Skema Penomoran <span><?= count($schemes) ?></span>
        </button>
        <button type="button" class="<?= $activeTab === 'workflows' ? 'active' : '' ?>" onclick="window.switchSummaryTab('workflows', this)">
            <i class="ri-node-tree"></i> Alur Persetujuan <span><?= count($workflows) ?></span>
        </button>
    </div>

    <!-- TAB 1: TEMPLATE SURAT -->
    <div data-tab-pane="templates" style="<?= $activeTab === 'templates' ? 'display:block;' : 'display:none;' ?>">
        <div class="template-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:16px;">
            <?php foreach($templates as $t): ?>
                <article class="template-card" style="display:grid; grid-template-columns:100px 1fr;">
                    <div class="template-preview <?= $t[6] ?>" style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                        <i class="<?= $t[0] ?>" style="font-size:32px;"></i>
                        <span style="font-size:16px; font-weight:800;"><?= $t[2] ?></span>
                    </div>
                    <div class="template-body">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <span class="status-chip <?= $t[5]==='Aktif'?'teal':'amber' ?>"><?= $t[5] ?></span>
                            <small style="color:var(--muted); font-size:9px;">VERSI <?= $t[3] ?></small>
                        </div>
                        <h2 style="font-size:15px; font-weight:800; margin:4px 0 6px;"><?= $t[1] ?></h2>
                        <p style="font-size:11px; color:var(--muted); line-height:1.5; margin:0 0 12px;"><?= $t[7] ?></p>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; border-top:1px solid var(--border); padding-top:8px;">
                            <small style="font-size:9px; color:var(--muted); display:block;">TERAKHIR DIUBAH</small>
                            <b style="font-size:11px;"><?= $t[4] ?></b>
                        </div>
                        <footer style="margin-top:12px; padding-top:10px; display:flex; justify-content:space-between; align-items:center;">
                            <button class="secondary-button" style="padding:6px 12px; font-size:11px;"><i class="ri-eye-line"></i> Pratinjau</button>
                            <a href="?page=letter-editor" class="primary-button" style="padding:6px 12px; font-size:11px; text-decoration:none;"><i class="ri-edit-line"></i> Buka Editor</a>
                        </footer>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TAB 2: SKEMA PENOMORAN -->
    <section class="panel" data-tab-pane="numbering" style="<?= $activeTab === 'numbering' ? 'display:block;' : 'display:none;' ?>">
        <div class="action-bar">
            <label class="search-field">
                <i class="ri-search-line"></i>
                <input data-table-search type="search" placeholder="Cari pola, kode, atau unit...">
            </label>
            <div class="filter-group">
                <button class="secondary-button"><i class="ri-refresh-line"></i> Reset Counter Tahunan</button>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>NAMA DOKUMEN</th>
                        <th>KODE JENIS</th>
                        <th>FORMAT / POLA PENOMORAN</th>
                        <th>UNIT CAKUPAN</th>
                        <th>NOMOR TERAKHIR</th>
                        <th>CONTOH NOMOR TERCETAK</th>
                        <th>STATUS</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($schemes as $s): ?>
                        <tr data-row>
                            <td><code><?= e($s[0]) ?></code></td>
                            <td><strong><?= e($s[1]) ?></strong></td>
                            <td><span class="badge blue"><?= e($s[2]) ?></span></td>
                            <td><code style="background:var(--ground); color:var(--primary); padding:3px 6px; border-radius:4px; font-size:11px;"><?= e($s[3]) ?></code></td>
                            <td><?= e($s[4]) ?></td>
                            <td><strong style="color:var(--primary); font-family:var(--font-mono, monospace);"><?= e($s[5]) ?></strong></td>
                            <td><span style="font-family:var(--font-mono, monospace); font-size:11px; color:var(--ink);"><?= e($s[6]) ?></span></td>
                            <td><span class="status-chip teal"><?= e($s[7]) ?></span></td>
                            <td><button class="row-action"><i class="ri-more-2-fill"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- TAB 3: ALUR PERSETUJUAN (WORKFLOW) -->
    <section class="panel" data-tab-pane="workflows" style="<?= $activeTab === 'workflows' ? 'display:block;' : 'display:none;' ?>">
        <div class="action-bar">
            <label class="search-field">
                <i class="ri-search-line"></i>
                <input data-table-search type="search" placeholder="Cari workflow persetujuan...">
            </label>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>NAMA ALUR KERJA</th>
                        <th>JENIS DOKUMEN</th>
                        <th>TAHAPAN HIERARKI PERSETUJUAN</th>
                        <th>TARGET SLA</th>
                        <th>STATUS</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($workflows as $wf): ?>
                        <tr data-row>
                            <td><code><?= e($wf['id']) ?></code></td>
                            <td><strong><?= e($wf['name']) ?></strong></td>
                            <td><span class="badge violet"><?= e($wf['type']) ?></span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                    <?php foreach($wf['steps'] as $idx => $step): ?>
                                        <span style="font-size:10.5px; background:var(--ground); border:1px solid var(--border); padding:2px 8px; border-radius:12px; font-weight:600;">
                                            <?= ($idx + 1) . '. ' . e($step) ?>
                                        </span>
                                        <?php if ($idx < count($wf['steps']) - 1): ?>
                                            <i class="ri-arrow-right-s-line" style="color:var(--muted); font-size:12px;"></i>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td><span class="status-chip blue"><?= e($wf['sla']) ?></span></td>
                            <td><span class="status-chip teal"><?= e($wf['status']) ?></span></td>
                            <td><button class="row-action"><i class="ri-more-2-fill"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
