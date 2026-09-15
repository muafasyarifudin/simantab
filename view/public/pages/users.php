<?php
declare(strict_types=1);

$activeTab = $_GET['tab'] ?? ($_GET['page'] === 'units' ? 'units' : 'users');

// 1. Data Pengguna
$people = [
    ['AF', 'Ahmad Fauzi, S.Kom.', '198907102019031004', 'LPSI', 'Administrator Instansi', 'Aktif'],
    ['RK', 'Rina Kusuma, S.E.', '199101122020122002', 'LPSI', 'Pembuat Surat', 'Aktif'],
    ['BS', 'Budi Santoso, M.Si.', '198405052010011003', 'LPPM', 'Kepala Satuan Kerja', 'Aktif'],
    ['DR', 'Dwi Rahmawati, S.H.', '199203182021022001', 'Biro Umum', 'Tata Usaha', 'Aktif'],
    ['HA', 'Hendra Ardiansyah, M.T.', '198804152015041002', 'Fakultas Teknik', 'Kepala Satuan Kerja', 'Aktif'],
    ['SN', 'Siti Nurhaliza, M.Pd.', '199308202022032005', 'BAAK', 'Tata Usaha', 'Aktif'],
    ['AP', 'Agus Prasetyo, S.Kom.', '199506102023011001', 'LPSI', 'Pembuat Surat', 'Aktif'],
];

// 2. Data Unit Organisasi
$units = [
    ['U-01', 'Lembaga Pengembangan Sistem Informasi', 'LPSI', 'Ahmad Fauzi, S.Kom.', '14 Pegawai', 'Aktif', 'blue'],
    ['U-02', 'Lembaga Penelitian & Pengabdian Masyarakat', 'LPPM', 'Dr. Budi Santoso, M.Si.', '22 Pegawai', 'Aktif', 'violet'],
    ['U-03', 'Biro Administrasi Akademik & Kemahasiswaan', 'BAAK', 'Drs. H. Mulyadi, M.M.', '18 Pegawai', 'Aktif', 'teal'],
    ['U-04', 'Biro Umum, Keuangan & Kepegawaian', 'Biro Umum', 'Dwi Rahmawati, S.H.', '26 Pegawai', 'Aktif', 'amber'],
    ['U-05', 'Fakultas Teknik & Ilmu Komputer', 'FTIK', 'Hendra Ardiansyah, M.T.', '35 Dosen & Staf', 'Aktif', 'indigo'],
    ['U-06', 'Fakultas Ekonomi & Bisnis', 'FEB', 'Dr. Maya Sartika, S.E., M.Ak.', '42 Dosen & Staf', 'Aktif', 'rose'],
];

// 3. Data Jabatan
$positions = [
    ['JAB-001', 'Kepala Lembaga / Biro', 'Struktural Eselon II', 'LPSI / LPPM / Biro', '5 Pejabat', 'Aktif'],
    ['JAB-002', 'Sekretaris Unit / Subbag', 'Struktural Eselon III', 'Semua Unit Kerja', '8 Pejabat', 'Aktif'],
    ['JAB-003', 'Verifikator Administrasi & Tata Usaha', 'Fungsional Umum', 'Tata Usaha', '12 Pegawai', 'Aktif'],
    ['JAB-004', 'Pengolah Data Sistem & Jaringan', 'Fungsional Tertentu', 'LPSI', '6 Pegawai', 'Aktif'],
    ['JAB-005', 'Dosen Pengajar & Peneliti', 'Fungsional Akademik', 'Fakultas', '140 Dosen', 'Aktif'],
    ['JAB-006', 'Staf Administrasi Persuratan', 'Pelaksana Teknis', 'Semua Satuan Kerja', '19 Pegawai', 'Aktif'],
];

// 4. Data Peran & Hak Akses
$roles = [
    ['Super Admin', 'super_admin', 'Akses penuh seluruh modul, manajemen instansi, konfigurasi database, dan audit keamanan.', '2 Akun', 'Level 1 (Tertinggi)', 'Aktif'],
    ['Administrator Instansi', 'admin_instansi', 'Manajemen pengguna instansi, unit organisasi, template surat, skema penomoran, dan audit log.', '4 Akun', 'Level 2', 'Aktif'],
    ['Kepala Satuan Kerja', 'kepala_unit', 'Persetujuan berjenjang dokumen unit, disposisi surat masuk, dan monitoring tugas staf.', '18 Akun', 'Level 3', 'Aktif'],
    ['Tata Usaha / Verifikator', 'tata_usaha', 'Pemeriksaan draft surat, validasi nomor surat, distribusi dokumen, dan pengarsipan digital.', '15 Akun', 'Level 3', 'Aktif'],
    ['Pembuat Surat', 'creator', 'Penyusunan draft surat resmi, surat tugas, SK, dan pengajuan ke alur persetujuan.', '45 Akun', 'Level 4', 'Aktif'],
    ['Pegawai / Pelaksana', 'employee', 'Penerima penugasan, pengisian laporan tindak lanjut kegiatan, dan akses agenda.', '85 Akun', 'Level 4', 'Aktif'],
];
?>

<div class="page-shell">
    <header class="page-header">
        <div>
            <p class="eyebrow">ADMINISTRASI / DATA MASTER</p>
            <h1>Pengguna & Unit Organisasi<span>.</span></h1>
            <p>Kelola akun pengguna, struktur organisasi, jabatan fungsional/struktural, dan hak akses RBAC.</p>
        </div>
        <button class="primary-button" id="mainPageActionBtn" 
            data-users-text="<i class='ri-user-add-line'></i> Tambah Pengguna"
            data-units-text="<i class='ri-community-line'></i> Tambah Unit"
            data-positions-text="<i class='ri-medal-line'></i> Tambah Jabatan"
            data-roles-text="<i class='ri-shield-user-line'></i> Tambah Peran">
            <i class="ri-user-add-line"></i> Tambah Pengguna
        </button>
    </header>

    <!-- Interactive Navigation Tabs -->
    <div class="summary-tabs">
        <button type="button" class="<?= $activeTab === 'users' ? 'active' : '' ?>" onclick="window.switchSummaryTab('users', this)">
            <i class="ri-user-3-line"></i> Pengguna <span><?= count($people) ?></span>
        </button>
        <button type="button" class="<?= $activeTab === 'units' ? 'active' : '' ?>" onclick="window.switchSummaryTab('units', this)">
            <i class="ri-building-4-line"></i> Unit Organisasi <span><?= count($units) ?></span>
        </button>
        <button type="button" class="<?= $activeTab === 'positions' ? 'active' : '' ?>" onclick="window.switchSummaryTab('positions', this)">
            <i class="ri-medal-2-line"></i> Jabatan <span><?= count($positions) ?></span>
        </button>
        <button type="button" class="<?= $activeTab === 'roles' ? 'active' : '' ?>" onclick="window.switchSummaryTab('roles', this)">
            <i class="ri-shield-keyhole-line"></i> Peran & Akses <span><?= count($roles) ?></span>
        </button>
    </div>

    <!-- TAB 1: PENGGUNA -->
    <section class="panel" data-tab-pane="users" style="<?= $activeTab === 'users' ? 'display:block;' : 'display:none;' ?>">
        <div class="action-bar">
            <label class="search-field">
                <i class="ri-search-line"></i>
                <input data-table-search type="search" placeholder="Cari nama, NIP, unit kerja, atau peran...">
            </label>
            <div class="filter-group">
                <select data-status-filter>
                    <option value="">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
                <button class="secondary-button"><i class="ri-upload-2-line"></i>Impor Data</button>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PENGGUNA</th>
                        <th>NIP / IDENTITAS</th>
                        <th>UNIT KERJA</th>
                        <th>PERAN UTAMA</th>
                        <th>STATUS</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($people as $p): ?>
                        <tr data-row data-status="<?= e($p[5]) ?>">
                            <td>
                                <div class="user-cell">
                                    <span class="avatar small"><?= e($p[0]) ?></span>
                                    <div>
                                        <strong><?= e($p[1]) ?></strong>
                                        <small><?= strtolower(str_replace(' ', '.', $p[1])) ?>@umpo.ac.id</small>
                                    </div>
                                </div>
                            </td>
                            <td><code><?= e($p[2]) ?></code></td>
                            <td><span class="badge blue" style="font-weight:600;"><?= e($p[3]) ?></span></td>
                            <td><?= e($p[4]) ?></td>
                            <td><span class="status-chip teal"><?= e($p[5]) ?></span></td>
                            <td>
                                <button class="row-action" title="Aksi Pengguna"><i class="ri-more-2-fill"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-foot">
            <span>Menampilkan <?= count($people) ?> pengguna terdaftar</span>
            <div class="pagination">
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
            </div>
        </div>
    </section>

    <!-- TAB 2: UNIT ORGANISASI -->
    <section class="panel" data-tab-pane="units" style="<?= $activeTab === 'units' ? 'display:block;' : 'display:none;' ?>">
        <div class="action-bar">
            <label class="search-field">
                <i class="ri-search-line"></i>
                <input data-table-search type="search" placeholder="Cari nama unit, singkatan, kode, atau pimpinan...">
            </label>
            <div class="filter-group">
                <select>
                    <option value="">Semua Tingkat</option>
                    <option value="Lembaga">Lembaga / Biro</option>
                    <option value="Fakultas">Fakultas</option>
                </select>
                <button class="secondary-button"><i class="ri-download-2-line"></i>Ekspor CSV</button>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>NAMA UNIT KERJA</th>
                        <th>SINGKATAN</th>
                        <th>PIMPINAN / KEPALA</th>
                        <th>TOTAL ANGGOTA</th>
                        <th>STATUS</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($units as $u): ?>
                        <tr data-row>
                            <td><code><?= e($u[0]) ?></code></td>
                            <td>
                                <strong><?= e($u[1]) ?></strong>
                                <small>Satuan Kerja Resmi Instansi</small>
                            </td>
                            <td><span class="badge <?= $u[6] ?>" style="font-weight:700;"><?= e($u[2]) ?></span></td>
                            <td><?= e($u[3]) ?></td>
                            <td><?= e($u[4]) ?></td>
                            <td><span class="status-chip teal"><?= e($u[5]) ?></span></td>
                            <td>
                                <button class="row-action" title="Opsi Unit"><i class="ri-more-2-fill"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-foot">
            <span>Menampilkan <?= count($units) ?> unit kerja organisasi</span>
        </div>
    </section>

    <!-- TAB 3: JABATAN -->
    <section class="panel" data-tab-pane="positions" style="<?= $activeTab === 'positions' ? 'display:block;' : 'display:none;' ?>">
        <div class="action-bar">
            <label class="search-field">
                <i class="ri-search-line"></i>
                <input data-table-search type="search" placeholder="Cari kode atau nama jabatan...">
            </label>
            <div class="filter-group">
                <select>
                    <option value="">Semua Kategori</option>
                    <option value="Struktural">Struktural</option>
                    <option value="Fungsional">Fungsional</option>
                </select>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>NAMA JABATAN</th>
                        <th>KATEGORI / TINGKAT</th>
                        <th>PENEMPATAN UNIT</th>
                        <th>PEJABAT AKTIF</th>
                        <th>STATUS</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($positions as $pos): ?>
                        <tr data-row>
                            <td><code><?= e($pos[0]) ?></code></td>
                            <td><strong><?= e($pos[1]) ?></strong></td>
                            <td><span class="status-chip violet"><?= e($pos[2]) ?></span></td>
                            <td><?= e($pos[3]) ?></td>
                            <td><?= e($pos[4]) ?></td>
                            <td><span class="status-chip teal"><?= e($pos[5]) ?></span></td>
                            <td>
                                <button class="row-action"><i class="ri-more-2-fill"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- TAB 4: PERAN & AKSES (RBAC) -->
    <section class="panel" data-tab-pane="roles" style="<?= $activeTab === 'roles' ? 'display:block;' : 'display:none;' ?>">
        <div class="action-bar">
            <label class="search-field">
                <i class="ri-search-line"></i>
                <input data-table-search type="search" placeholder="Cari nama peran atau wewenang...">
            </label>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NAMA PERAN</th>
                        <th>SLUG IDENTIFIER</th>
                        <th>DESKRIPSI WEWENANG</th>
                        <th>JUMLAH AKUN</th>
                        <th>TINGKAT OTORITAS</th>
                        <th>STATUS</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($roles as $r): ?>
                        <tr data-row>
                            <td><strong><?= e($r[0]) ?></strong></td>
                            <td><code><?= e($r[1]) ?></code></td>
                            <td style="max-width:340px; white-space:normal; font-size:12px; line-height:1.5; color:var(--muted);"><?= e($r[2]) ?></td>
                            <td><span class="badge blue"><?= e($r[3]) ?></span></td>
                            <td><span class="status-chip amber"><?= e($r[4]) ?></span></td>
                            <td><span class="status-chip teal"><?= e($r[5]) ?></span></td>
                            <td>
                                <button class="row-action"><i class="ri-more-2-fill"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
