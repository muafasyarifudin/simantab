<?php
RbacService::authorize('documents.create');
$db = Database::connection();
$u = AuthService::requireUser();
$formError = null;

// Ambil data referensi
$types = $db->prepare('SELECT id,name,code FROM document_types WHERE (institution_id=? OR institution_id IS NULL) AND is_active=1 ORDER BY sort_order');
$types->execute([$u['institution_id']]);
$types = $types->fetchAll();

$units = $db->prepare('SELECT id,name,short_name FROM organization_units WHERE institution_id=? AND is_active=1 ORDER BY name');
$units->execute([$u['institution_id']]);
$units = $units->fetchAll();

$archives = $db->prepare('SELECT id,code,name FROM archive_classifications WHERE institution_id=? AND is_active=1 ORDER BY code');
$archives->execute([$u['institution_id']]);
$archives = $archives->fetchAll();

$schemes = $db->prepare('SELECT id,name,format_pattern FROM numbering_schemes WHERE institution_id=? AND is_active=1 ORDER BY name');
$schemes->execute([$u['institution_id']]);
$schemes = $schemes->fetchAll();

$instQuery = $db->prepare('SELECT name,short_name,address,phone,email,website,logo_path FROM institutions WHERE id=?');
$instQuery->execute([$u['institution_id']]);
$institution = $instQuery->fetch() ?: [];

// Penandatangan dari daftar pegawai
$signers = $db->prepare('SELECT e.id,e.full_name,e.employee_no,p.name as position_name FROM employees e LEFT JOIN employee_unit_histories euh ON euh.employee_id=e.id AND euh.is_primary=1 LEFT JOIN positions p ON p.id=euh.position_id WHERE e.institution_id=? AND e.is_active=1 ORDER BY e.full_name');
$signers->execute([$u['institution_id']]);
$signers = $signers->fetchAll();

// Default Values
$defaultInst1 = $institution['name'] ?? 'UNIVERSITAS MUHAMMADIYAH PONOROGO';
$defaultUnit = 'Lembaga Pengembangan Sistem Informasi (LPSI)';
$defaultAddress = $institution['address'] ?? 'Jl. Budi Utomo No. 10, Ronowijayan, Kec. Siman, Kab. Ponorogo, Jawa Timur 63471';
$defaultContact = implode(' | ', array_filter([$institution['phone'] ?? 'Telp. (0352) 481124', $institution['email'] ?? 'info@umpo.ac.id', $institution['website'] ?? 'www.umpo.ac.id']));
$defaultCity = 'Ponorogo';
$defaultDate = date('Y-m-d');
$defaultSignerName = !empty($u['employee_id']) ? ($u['username'] ?? 'Ahmad Fauzi, M.Kom.') : 'Ahmad Fauzi, M.Kom.';

// Handle Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Security::verifyCsrf($_POST['_token'] ?? null);

        $isManualNumber = ($_POST['number_mode'] ?? 'auto') === 'manual';
        $customNumber = $isManualNumber ? trim((string)($_POST['custom_number'] ?? '')) : null;

        $letterData = [
            'institution' => trim((string)($_POST['institution'] ?? $defaultInst1)),
            'unit' => trim((string)($_POST['unit'] ?? $defaultUnit)),
            'address' => trim((string)($_POST['address'] ?? $defaultAddress)),
            'contact' => trim((string)($_POST['contact'] ?? $defaultContact)),
            'heading' => trim((string)($_POST['heading'] ?? 'SURAT TUGAS')),
            'kop_align' => $_POST['kop_align'] ?? 'center',
            'kop_line_style' => $_POST['kop_line_style'] ?? 'double',
            'logo_align' => $_POST['logo_align'] ?? 'left',
            'logo_size' => $_POST['logo_size'] ?? 'medium',
            'font_family' => $_POST['font_family'] ?? 'serif',
            'custom_number' => $customNumber ?: '',
            'confidentiality' => $_POST['confidentiality'] ?? 'Biasa',
            'attachment' => trim((string)($_POST['attachment'] ?? '-')),
            'title' => trim((string)($_POST['title'] ?? '')),
            'recipient' => trim((string)($_POST['recipient'] ?? '')),
            'greeting_opening' => trim((string)($_POST['greeting_opening'] ?? 'Dengan hormat,')),
            'opening' => trim((string)($_POST['opening'] ?? '')),
            'body' => trim((string)($_POST['body'] ?? '')),
            'closing' => trim((string)($_POST['closing'] ?? '')),
            'greeting_closing' => trim((string)($_POST['greeting_closing'] ?? 'Hormat kami,')),
            'city' => trim((string)($_POST['city'] ?? $defaultCity)),
            'date' => $_POST['document_date'] ?? $defaultDate,
            'signer_position' => trim((string)($_POST['signer_position'] ?? 'Kepala LPSI')),
            'signer_name' => trim((string)($_POST['signer_name'] ?? $defaultSignerName)),
            'signer_number' => trim((string)($_POST['signer_number'] ?? 'NIDN. 0712058801')),
            'signature_type' => $_POST['signature_type'] ?? 'image',
            'stamp_position' => $_POST['stamp_position'] ?? 'left',
            'stamp_opacity' => $_POST['stamp_opacity'] ?? '85',
            'copies' => trim((string)($_POST['copies'] ?? '')),
            'footer_note' => trim((string)($_POST['footer_note'] ?? '')),
            'logo' => LetterService::saveBase64Image((string)($_POST['logo_base64'] ?? ''), 'logo'),
            'logo_right' => LetterService::saveBase64Image((string)($_POST['logo_right_base64'] ?? ''), 'logo_r'),
            'signature' => LetterService::saveBase64Image((string)($_POST['signature_base64'] ?? ''), 'sign'),
            'stamp' => LetterService::saveBase64Image((string)($_POST['stamp_base64'] ?? ''), 'stamp')
        ];

        // Process file uploads if base64 wasn't set by JS
        if (empty($letterData['logo']) && isset($_FILES['logo_file']) && ($_FILES['logo_file']['error'] ?? 4) === 0) {
            $letterData['logo'] = LetterService::upload($_FILES['logo_file'], 'logo');
        }
        if (empty($letterData['signature']) && isset($_FILES['signature_file']) && ($_FILES['signature_file']['error'] ?? 4) === 0) {
            $letterData['signature'] = LetterService::upload($_FILES['signature_file'], 'sign');
        }
        if (empty($letterData['stamp']) && isset($_FILES['stamp_file']) && ($_FILES['stamp_file']['error'] ?? 4) === 0) {
            $letterData['stamp'] = LetterService::upload($_FILES['stamp_file'], 'stamp');
        }

        $postContent = $_POST;
        // Strip heavy base64 strings so JSON payload stays tiny (< 5 KB) and prevents MySQL packet limits / 2006 errors
        unset($postContent['logo_base64'], $postContent['logo_right_base64'], $postContent['signature_base64'], $postContent['stamp_base64']);
        $postContent['letter'] = $letterData;

        $doc = DocumentService::create([
            'owner_unit_id' => (int)$_POST['owner_unit_id'],
            'document_type_id' => (int)$_POST['document_type_id'],
            'number' => $customNumber,
            'numbering_scheme_id' => $_POST['numbering_scheme_id'] !== '' ? (int)$_POST['numbering_scheme_id'] : null,
            'archive_classification_id' => $_POST['archive_classification_id'] !== '' ? (int)$_POST['archive_classification_id'] : null,
            'title' => trim($_POST['title'] ?? ''),
            'summary' => trim($_POST['body'] ?? $_POST['summary'] ?? ''),
            'confidentiality' => $_POST['confidentiality'] ?? 'normal',
            'priority' => $_POST['priority'] ?? 'normal',
            'document_date' => $_POST['document_date'] ?? date('Y-m-d'),
            'effective_from' => !empty($_POST['effective_from']) ? $_POST['effective_from'] : null,
            'effective_until' => !empty($_POST['effective_until']) ? $_POST['effective_until'] : null,
            'report_due_at' => !empty($_POST['report_due_at']) ? $_POST['report_due_at'] : null,
            'report_type' => $_POST['report_type'] ?? 'none',
            'signer_employee_id' => !empty($_POST['signer_employee_id']) ? (int)$_POST['signer_employee_id'] : null,
            'content' => $postContent
        ]);

        if (!headers_sent()) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Location: index.php?page=document-detail&id=' . (int)$doc['id']);
            exit;
        }
        echo '<meta http-equiv="refresh" content="0;url=index.php?page=document-detail&id=' . (int)$doc['id'] . '">';
        echo '<script>window.location.replace("index.php?page=document-detail&id=' . (int)$doc['id'] . '");</script>';
        exit;
    } catch (Throwable $e) {
        $formError = $e->getMessage();
    }
}
?>

<div class="page-shell full-width builder-shell">
    <div class="builder-header">
        <div class="builder-header-left">
            <p class="eyebrow">MANAJEMEN SURAT / STUDIO PERSURATAN</p>
            <h1>Buat Surat Resmi<span>.</span></h1>
            <p>Konfigurasi penuh KOP, Logo, Garis Dinas, Identitas Surat, Konten, Tanda Tangan & Cap secara langsung (live preview).</p>
        </div>
        <div class="builder-view-toggle">
            <button type="button" class="active" data-view-mode="split" onclick="window.setWorkViewMode('split'); return false;"><i class="ri-layout-column-line"></i>Split Workbench</button>
            <button type="button" data-view-mode="form" onclick="window.setWorkViewMode('form'); return false;"><i class="ri-edit-box-line"></i>Form Saja</button>
            <button type="button" data-view-mode="preview" onclick="window.setWorkViewMode('preview'); return false;"><i class="ri-file-paper-line"></i>Kertas A4</button>
        </div>
    </div>

    <?php if ($formError): ?>
        <div class="auth-error" role="alert" style="margin-bottom:18px;"><i class="ri-error-warning-line"></i> <?= e($formError) ?></div>
    <?php endif; ?>

    <form id="letterBuilderForm" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= e(Security::csrfToken()) ?>">
        <!-- Base64 hidden inputs for live sync and upload (retained if re-rendered) -->
        <input type="hidden" name="logo_base64" id="logo_base64" value="<?= e($_POST['logo_base64'] ?? '') ?>">
        <input type="hidden" name="logo_right_base64" id="logo_right_base64" value="<?= e($_POST['logo_right_base64'] ?? '') ?>">
        <input type="hidden" name="signature_base64" id="signature_base64" value="<?= e($_POST['signature_base64'] ?? '') ?>">
        <input type="hidden" name="stamp_base64" id="stamp_base64" value="<?= e($_POST['stamp_base64'] ?? '') ?>">

        <div class="builder-workbench" id="builderWorkbench">
            <!-- KOLOM KIRI: Form Controls & Customization -->
            <div class="builder-form-col">
                <!-- Navigation Tabs -->
                <div class="builder-tabs-nav" role="tablist">
                    <button type="button" class="builder-tab-btn active" data-tab="tab-meta" onclick="window.switchBuilderTab(0); return false;"><i class="ri-file-text-line"></i> 1. Identitas & Nomor</button>
                    <button type="button" class="builder-tab-btn" data-tab="tab-kop" onclick="window.switchBuilderTab(1); return false;"><i class="ri-layout-top-line"></i> 2. KOP, Logo & Garis</button>
                    <button type="button" class="builder-tab-btn" data-tab="tab-content" onclick="window.switchBuilderTab(2); return false;"><i class="ri-edit-line"></i> 3. Tujuan & Isi Surat</button>
                    <button type="button" class="builder-tab-btn" data-tab="tab-sign" onclick="window.switchBuilderTab(3); return false;"><i class="ri-shield-check-line"></i> 4. Tanda Tangan & Cap</button>
                </div>

                <!-- TAB 1: IDENTITAS & NOMOR SURAT -->
                <div class="builder-tab-pane active" id="tab-meta" style="display: block;">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-file-settings-line"></i></span>
                            <div>
                                <h3>Informasi Utama & Penomoran Dokumen</h3>
                                <p>Tentukan jenis surat, nomor resmi, dan perihal dokumen.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Jenis Surat <b>*</b></label>
                                <select name="document_type_id" id="document_type_id" required>
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    <?php foreach ($types as $x): ?>
                                        <option value="<?= $x['id'] ?>" data-code="<?= e($x['code']) ?>" <?= strtolower($x['name']) === 'surat tugas' ? 'selected' : '' ?>><?= e($x['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Judul Kategori / Heading Kertas</label>
                                <input type="text" name="heading" id="heading" value="SURAT TUGAS" placeholder="Contoh: SURAT TUGAS / SURAT EDARAN">
                            </div>

                            <div class="builder-field full">
                                <label>Mode Penomoran Surat</label>
                                <div class="pill-selector">
                                    <label class="pill-opt selected">
                                        <input type="radio" name="number_mode" value="auto" checked>
                                        <span><i class="ri-magic-line"></i> Otomatis dari Sistem / Skema</span>
                                    </label>
                                    <label class="pill-opt">
                                        <input type="radio" name="number_mode" value="manual">
                                        <span><i class="ri-pencil-line"></i> Kustom / Manual Ketik Sendiri</span>
                                    </label>
                                </div>
                            </div>

                            <div class="builder-field full" id="manualNumberWrap" style="display:none;">
                                <label>Nomor Surat Kustom <b>*</b></label>
                                <input type="text" name="custom_number" id="custom_number" placeholder="Contoh: 045.2/LPSI-UMP/IX/2026" maxlength="160">
                                <small>Ketikkan nomor surat lengkap sesuai format tata naskah dinas Anda.</small>
                            </div>

                            <div class="builder-field" id="schemeWrap">
                                <label>Skema Penomoran Sistem</label>
                                <select name="numbering_scheme_id">
                                    <option value="">Ditentukan saat verifikasi dinas</option>
                                    <?php foreach ($schemes as $x): ?>
                                        <option value="<?= $x['id'] ?>"><?= e($x['name']) ?> (<?= e($x['format_pattern']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Sifat Surat <b>*</b></label>
                                <select name="confidentiality" id="confidentiality" required>
                                    <option value="Biasa" selected>Biasa</option>
                                    <option value="Penting">Penting</option>
                                    <option value="Segera">Segera</option>
                                    <option value="Rahasia">Rahasia</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Lampiran</label>
                                <input type="text" name="attachment" id="attachment" value="-" placeholder="Contoh: - atau 1 (satu) Berkas">
                            </div>

                            <div class="builder-field">
                                <label>Tanggal Surat <b>*</b></label>
                                <input type="date" name="document_date" id="document_date" value="<?= $defaultDate ?>" required>
                            </div>

                            <div class="builder-field full">
                                <label>Perihal / Hal Surat <b>*</b></label>
                                <input type="text" name="title" id="title" required maxlength="500" placeholder="Contoh: Penugasan Tim Audit Infrastruktur TI & Keamanan Siber" value="Penugasan Tim Audit Infrastruktur TI & Keamanan Siber">
                            </div>

                            <div class="builder-field">
                                <label>Unit Pembuat <b>*</b></label>
                                <select name="owner_unit_id" id="owner_unit_id" required>
                                    <?php foreach ($units as $x): ?>
                                        <option value="<?= $x['id'] ?>" <?= $u['unit_id'] == $x['id'] ? 'selected' : '' ?>><?= e($x['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Klasifikasi Arsip</label>
                                <select name="archive_classification_id">
                                    <option value="">Pilih Klasifikasi</option>
                                    <?php foreach ($archives as $x): ?>
                                        <option value="<?= $x['id'] ?>"><?= e($x['code'] . ' · ' . $x['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Pelaksanaan / Tambahan -->
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon violet"><i class="ri-calendar-event-line"></i></span>
                            <div>
                                <h3>Masa Berlaku & Laporan (Opsional)</h3>
                                <p>Digunakan untuk surat tugas dan monitoring penugasan.</p>
                            </div>
                        </div>
                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Mulai Berlaku</label>
                                <input name="effective_from" type="date">
                            </div>
                            <div class="builder-field">
                                <label>Selesai Berlaku</label>
                                <input name="effective_until" type="date">
                            </div>
                            <div class="builder-field">
                                <label>Jenis Laporan</label>
                                <select name="report_type">
                                    <option value="none">Tidak Wajib</option>
                                    <option value="individual">Individu</option>
                                    <option value="team">Tim</option>
                                </select>
                            </div>
                            <div class="builder-field">
                                <label>Batas Waktu Laporan</label>
                                <input name="report_due_at" type="datetime-local">
                            </div>
                        </div>
                    </section>
                </div>

                <!-- TAB 2: KOP, LOGO & GARIS PEMBATAS -->
                <div class="builder-tab-pane" id="tab-kop" style="display: none;">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-layout-top-line"></i></span>
                            <div>
                                <h3>Kepala Surat (KOP) & Tipografi</h3>
                                <p>Kustomisasi teks KOP, instansi, alamat, dan pilihan font.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Nama Lembaga Induk / Instansi Atas (Baris 1) <b>*</b></label>
                            <input type="text" name="institution" id="kop_inst1" value="<?= e($defaultInst1) ?>" required>
                        </div>

                        <div class="builder-field full">
                            <label>Nama Satuan Kerja / Unit / Fakultas (Baris 2)</label>
                            <input type="text" name="unit" id="kop_inst2" value="<?= e($defaultUnit) ?>">
                        </div>

                        <div class="builder-field full">
                            <label>Alamat Lengkap KOP <b>*</b></label>
                            <textarea name="address" id="kop_address" rows="2" required><?= e($defaultAddress) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Kontak KOP (Telepon, Email, Website)</label>
                            <input type="text" name="contact" id="kop_contact" value="<?= e($defaultContact) ?>">
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Perataan KOP (Alignment)</label>
                                <div class="pill-selector">
                                    <label class="pill-opt selected">
                                        <input type="radio" name="kop_align" value="center" checked>
                                        <span><i class="ri-align-center"></i> Tengah (Standar)</span>
                                    </label>
                                    <label class="pill-opt">
                                        <input type="radio" name="kop_align" value="left">
                                        <span><i class="ri-align-left"></i> Rata Kiri</span>
                                    </label>
                                </div>
                            </div>

                            <div class="builder-field">
                                <label>Gaya Huruf Dokumen (Font)</label>
                                <div class="pill-selector">
                                    <label class="pill-opt selected">
                                        <input type="radio" name="font_family" value="serif" checked>
                                        <span>Serif (Times Formal)</span>
                                    </label>
                                    <label class="pill-opt">
                                        <input type="radio" name="font_family" value="sans">
                                        <span>Sans-Serif (Modern)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- LOGO & GARIS PEMBATAS KOP -->
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon emerald"><i class="ri-image-line"></i></span>
                            <div>
                                <h3>Logo & Garis Pembatas KOP</h3>
                                <p>Unggah lambang resmi dan atur garis pemisah standar dinas.</p>
                            </div>
                        </div>

                        <!-- Upload Logo Utama -->
                        <div class="builder-field full">
                            <label>Logo Resmi Instansi</label>
                            <div class="builder-upload-box">
                                <div class="builder-upload-preview" id="logoPreviewBox">
                                    <span class="placeholder-icon"><i class="ri-image-add-line"></i></span>
                                </div>
                                <div class="builder-upload-info">
                                    <strong>Pilih file Logo (PNG / JPG transparan)</strong>
                                    <p>Maksimal 2 MB. Disarankan rasio 1:1 atau logo transparan.</p>
                                    <div class="builder-upload-actions">
                                        <input type="file" id="logoFileInput" name="logo_file" accept="image/png,image/jpeg" style="display:none;">
                                        <button type="button" class="secondary-button" onclick="document.getElementById('logoFileInput').click();"><i class="ri-upload-2-line"></i>Pilih Gambar</button>
                                        <button type="button" class="secondary-button" id="removeLogoBtn" style="display:none;color:var(--rose);"><i class="ri-delete-bin-line"></i>Hapus Logo</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Posisi Logo pada KOP</label>
                                <select name="logo_align" id="logo_align">
                                    <option value="left" selected>Kiri Saja (Standar Dinas)</option>
                                    <option value="both">Dua Logo (Kiri & Kanan)</option>
                                    <option value="none">Tanpa Logo</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Ukuran Logo</label>
                                <select name="logo_size" id="logo_size">
                                    <option value="small">Kecil (56 px)</option>
                                    <option value="medium" selected>Sedang (72 px)</option>
                                    <option value="large">Besar (88 px)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Opsi Garis Pembatas KOP -->
                        <div class="builder-field full" style="margin-top:10px;">
                            <label>Garis Pembatas KOP (Letterhead Divider Line) <b>*</b></label>
                            <div class="pill-selector">
                                <label class="pill-opt selected">
                                    <input type="radio" name="kop_line_style" value="double" checked>
                                    <span>Garis Ganda Resmi (Tebal & Tipis)</span>
                                </label>
                                <label class="pill-opt">
                                    <input type="radio" name="kop_line_style" value="single_thick">
                                    <span>Garis Tunggal Tebal</span>
                                </label>
                                <label class="pill-opt">
                                    <input type="radio" name="kop_line_style" value="single_thin">
                                    <span>Garis Tunggal Tipis</span>
                                </label>
                                <label class="pill-opt">
                                    <input type="radio" name="kop_line_style" value="dashed">
                                    <span>Garis Putus-putus</span>
                                </label>
                                <label class="pill-opt">
                                    <input type="radio" name="kop_line_style" value="none">
                                    <span>Tanpa Garis</span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- TAB 3: TUJUAN, SALAM & ISI SURAT -->
                <div class="builder-tab-pane" id="tab-content" style="display: none;">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon amber"><i class="ri-mail-line"></i></span>
                            <div>
                                <h3>Penerima Surat & Salam Pembuka</h3>
                                <p>Tujuan surat kepada pejabat/mitra dan salam pembuka formal.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Tujuan / Penerima Surat (Kepada Yth.) <b>*</b></label>
                            <textarea name="recipient" id="recipient" rows="3" required placeholder="Contoh:&#10;Yth. Dekan Fakultas Teknik&#10;Universitas Muhammadiyah Ponorogo&#10;di Tempat">Yth. Seluruh Pejabat Struktural dan Tim Teknis LPSI
Universitas Muhammadiyah Ponorogo
di Tempat</textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Salam Pembuka (Greeting Opening)</label>
                            <div class="pill-selector" style="margin-bottom:8px;">
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Dengan hormat,'); return false;">Dengan hormat,</button>
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Assalamu\'alaikum Wr. Wb.,'); return false;">Assalamu'alaikum Wr. Wb.,</button>
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Salam sejahtera,'); return false;">Salam sejahtera,</button>
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Selamat pagi/siang,'); return false;">Selamat pagi,</button>
                            </div>
                            <input type="text" name="greeting_opening" id="greeting_opening" value="Dengan hormat,">
                        </div>

                        <div class="builder-field full">
                            <label>Paragraf Pembuka / Dasar Penerbitan Surat</label>
                            <textarea name="opening" id="opening" rows="3" placeholder="Contoh: Dalam rangka pelaksanaan audit kepatuhan infrastruktur sistem informasi semester ganjil tahun akademik 2026/2027, bersama surat ini pimpinan menugaskan:">Dalam rangka mewujudkan tata kelola teknologi informasi yang andal, aman, dan akuntabel di lingkungan Universitas Muhammadiyah Ponorogo, dengan ini pimpinan menugaskan kepada tim yang tercantum untuk melaksanakan kegiatan audit dan evaluasi infrastruktur sistem informasi.</textarea>
                        </div>
                    </section>

                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-file-list-3-line"></i></span>
                            <div>
                                <h3>Pokok Isi Surat & Penutup</h3>
                                <p>Rincian isi surat, poin instruksi/kegiatan, dan kalimat penutup.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Isi Dokumen / Rincian Penugasan <b>*</b></label>
                            <textarea name="body" id="body" rows="8" required placeholder="Tuliskan isi surat lengkap di sini...">Adapun ketentuan dan ruang lingkup pelaksanaan adalah sebagai berikut:
1. Melakukan pemeriksaan menyeluruh terhadap server database, jaringan internal, dan sistem cadangan data (backup recovery).
2. Mengidentifikasi kerentanan sistem dan menyusun rekomendasi mitigasi risiko keamanan siber.
3. Menyusun laporan hasil pelaksanaan tugas dan menyampaikannya kepada Rektor paling lambat 7 (tujuh) hari kerja setelah penugasan selesai.</textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Paragraf Penutup (Closing Statement)</label>
                            <textarea name="closing" id="closing" rows="2" placeholder="Contoh: Demikian surat tugas ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh tanggung jawab.">Demikian surat tugas ini diterbitkan untuk dilaksanakan dengan penuh rasa tanggung jawab dan dedikasi.</textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Salam Penutup</label>
                            <div class="pill-selector" style="margin-bottom:8px;">
                                <button type="button" class="pill-opt" onclick="window.setClosingGreeting('Hormat kami,'); return false;">Hormat kami,</button>
                                <button type="button" class="pill-opt" onclick="window.setClosingGreeting('Wassalamu\'alaikum Wr. Wb.'); return false;">Wassalamu'alaikum Wr. Wb.</button>
                                <button type="button" class="pill-opt" onclick="window.setClosingGreeting('Wassalam,'); return false;">Wassalam,</button>
                            </div>
                            <input type="text" name="greeting_closing" id="greeting_closing" value="Hormat kami,">
                        </div>
                    </section>
                </div>

                <!-- TAB 4: TANDA TANGAN, CAP & FOOTER -->
                <div class="builder-tab-pane" id="tab-sign" style="display: none;">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon violet"><i class="ri-quill-pen-line"></i></span>
                            <div>
                                <h3>Pejabat & Tanda Tangan</h3>
                                <p>Identitas penandatangan, tanda tangan digital, atau stempel basah.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Kota Penetapan Surat</label>
                                <input type="text" name="city" id="city" value="<?= e($defaultCity) ?>">
                            </div>

                            <div class="builder-field">
                                <label>Pilih Pegawai Penandatangan</label>
                                <select name="signer_employee_id" id="signer_employee_id">
                                    <option value="">-- Ketik manual di bawah --</option>
                                    <?php foreach ($signers as $s): ?>
                                        <option value="<?= $s['id'] ?>" data-name="<?= e($s['full_name']) ?>" data-nip="<?= e($s['employee_no']) ?>" data-pos="<?= e($s['position_name']) ?>"><?= e($s['full_name']) ?> (<?= e($s['position_name'] ?: 'Pejabat') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Jabatan Penandatangan <b>*</b></label>
                                <input type="text" name="signer_position" id="signer_position" value="Kepala LPSI" required>
                            </div>

                            <div class="builder-field">
                                <label>Nama Lengkap Pejabat <b>*</b></label>
                                <input type="text" name="signer_name" id="signer_name" value="<?= e($defaultSignerName) ?>" required>
                            </div>

                            <div class="builder-field full">
                                <label>NIP / NIDN / NBM Pejabat</label>
                                <input type="text" name="signer_number" id="signer_number" value="NIDN. 0712058801">
                            </div>
                        </div>

                        <!-- Upload Tanda Tangan -->
                        <div class="builder-field full" style="margin-top:10px;">
                            <label>Gambar Tanda Tangan (PNG Transparan)</label>
                            <div class="builder-upload-box">
                                <div class="builder-upload-preview" id="signPreviewBox">
                                    <span class="placeholder-icon"><i class="ri-quill-pen-line"></i></span>
                                </div>
                                <div class="builder-upload-info">
                                    <strong>Unggah Tanda Tangan Basah / Scan</strong>
                                    <p>Disarankan gambar berlatar belakang transparan (PNG).</p>
                                    <div class="builder-upload-actions">
                                        <input type="file" id="signFileInput" name="signature_file" accept="image/png,image/jpeg" style="display:none;">
                                        <button type="button" class="secondary-button" onclick="document.getElementById('signFileInput').click();"><i class="ri-upload-2-line"></i>Unggah Tanda Tangan</button>
                                        <button type="button" class="secondary-button" id="removeSignBtn" style="display:none;color:var(--rose);"><i class="ri-delete-bin-line"></i>Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- CAP / STEMPEL RESMI -->
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon rose"><i class="ri-copper-coin-line"></i></span>
                            <div>
                                <h3>Cap / Stempel Resmi Dinas</h3>
                                <p>Atur stempel dinas agar bertumpuk realistis dengan tanda tangan.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Gambar Cap / Stempel (PNG Transparan)</label>
                            <div class="builder-upload-box">
                                <div class="builder-upload-preview" id="stampPreviewBox">
                                    <span class="placeholder-icon"><i class="ri-copper-coin-line"></i></span>
                                </div>
                                <div class="builder-upload-info">
                                    <strong>Unggah Stempel Lembaga / Unit</strong>
                                    <p>Gunakan gambar cap berlatar transparan untuk efek tumpang-tindih resmi.</p>
                                    <div class="builder-upload-actions">
                                        <input type="file" id="stampFileInput" name="stamp_file" accept="image/png,image/jpeg" style="display:none;">
                                        <button type="button" class="secondary-button" onclick="document.getElementById('stampFileInput').click();"><i class="ri-upload-2-line"></i>Unggah Stempel</button>
                                        <button type="button" class="secondary-button" id="removeStampBtn" style="display:none;color:var(--rose);"><i class="ri-delete-bin-line"></i>Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Posisi Stempel terhadap TTD</label>
                                <select name="stamp_position" id="stamp_position">
                                    <option value="left" selected>Sebelah Kiri Menimpa TTD (Standar Resmi)</option>
                                    <option value="center">Tengah Tepat di TTD</option>
                                    <option value="right">Sebelah Kanan TTD</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Tingkat Ketebalan (Opasitas Stempel)</label>
                                <select name="stamp_opacity" id="stamp_opacity">
                                    <option value="100">100% (Pekat)</option>
                                    <option value="85" selected>85% (Realistis Tinta Basah)</option>
                                    <option value="70">70% (Transparan Halus)</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- TEMBUSAN & CATATAN KAKI -->
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon teal"><i class="ri-file-copy-line"></i></span>
                            <div>
                                <h3>Tembusan & Catatan Kaki (Footer)</h3>
                                <p>Daftar tembusan surat dan catatan integritas dokumen.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Tembusan Surat (Satu baris per penerima)</label>
                            <textarea name="copies" id="copies" rows="4" placeholder="Contoh:&#10;1. Rektor Universitas Muhammadiyah Ponorogo&#10;2. Wakil Rektor II Bidang Administrasi dan Keuangan&#10;3. Arsip">1. Rektor Universitas Muhammadiyah Ponorogo (sebagai laporan)
2. Wakil Rektor Terkait
3. Arsip LPSI</textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Catatan Kaki Dokumen (Footer Note)</label>
                            <input type="text" name="footer_note" id="footer_note" value="Dokumen resmi diterbitkan melalui SIMANTAP · Universitas Muhammadiyah Ponorogo" placeholder="Contoh: Dokumen ini telah diverifikasi elektronik melalui SIMANTAP">
                        </div>
                    </section>
                </div>

                <!-- Tombol Navigasi Wizard & Simpan -->
                <div class="builder-actions-bar">
                    <a class="secondary-button" href="?page=documents"><i class="ri-close-line"></i>Batal</a>
                    <div style="display:flex;gap:8px;">
                        <button type="button" class="secondary-button" id="prevTabBtn" style="display:none;" onclick="window.switchBuilderTab((window.currentBuilderTabIdx||0)-1); return false;"><i class="ri-arrow-left-line"></i>Kembali</button>
                        <button type="button" class="secondary-button" id="nextTabBtn" onclick="window.switchBuilderTab((window.currentBuilderTabIdx||0)+1); return false;">Lanjut ke KOP & Logo <i class="ri-arrow-right-line"></i></button>
                        <button class="primary-button" type="submit" id="submitDraftBtn"><i class="ri-save-3-line"></i>Simpan Draf Surat</button>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Realtime Live A4 Sheet Preview -->
            <div class="builder-preview-col" id="previewCol">
                <div class="preview-container-card">
                    <div class="preview-bar">
                        <div class="preview-bar-title">
                            <i class="ri-eye-line" style="color:var(--primary);"></i>
                            <span>Pratinjau Lembar Kertas A4 Resmi</span>
                        </div>
                        <div class="preview-bar-actions">
                            <button type="button" id="zoomOutBtn" title="Perkecil" onclick="window.applyA4Zoom(-10); return false;"><i class="ri-subtract-line"></i></button>
                            <b id="zoomLabel">100%</b>
                            <button type="button" id="zoomInBtn" title="Perbesar" onclick="window.applyA4Zoom(10); return false;"><i class="ri-add-line"></i></button>
                            <button type="button" id="zoomResetBtn" title="Reset Zoom" style="width:auto;padding:0 8px;font-size:11px;font-weight:700;" onclick="window.resetA4Zoom(); return false;">Reset</button>
                        </div>
                    </div>

                    <div class="a4-viewport" id="a4Viewport">
                        <!-- LEMBAR KERTAS A4 DINAS -->
                        <div class="a4-sheet" id="a4Sheet">
                            <div class="a4-draft-watermark">DRAF RESMI</div>

                            <!-- KOP SURAT -->
                            <table class="a4-kop-table" id="a4KopTable">
                                <tr>
                                    <td class="a4-kop-logo-left" id="a4LogoLeftCell" style="display:none;">
                                        <img src="" class="a4-kop-logo-img" id="a4LogoLeftImg" alt="Logo">
                                    </td>
                                    <td class="a4-kop-text" id="a4KopTextCell">
                                        <div class="a4-kop-inst-1" id="a4KopInst1">UNIVERSITAS MUHAMMADIYAH PONOROGO</div>
                                        <div class="a4-kop-inst-2" id="a4KopInst2">LEMBAGA PENGEMBANGAN SISTEM INFORMASI (LPSI)</div>
                                        <div class="a4-kop-address" id="a4KopAddress">Jl. Budi Utomo No. 10, Ronowijayan, Kec. Siman, Kab. Ponorogo, Jawa Timur 63471</div>
                                        <div class="a4-kop-contact" id="a4KopContact">Telp. (0352) 481124 | info@umpo.ac.id | www.umpo.ac.id</div>
                                    </td>
                                    <td class="a4-kop-logo-right" id="a4LogoRightCell" style="display:none;">
                                        <img src="" class="a4-kop-logo-img" id="a4LogoRightImg" alt="Logo Kanan">
                                    </td>
                                </tr>
                            </table>

                            <!-- GARIS PEMBATAS KOP -->
                            <div class="a4-line-double" id="a4KopLine"></div>

                            <!-- IDENTITAS SURAT & TANGGAL -->
                            <table class="a4-meta-table">
                                <tr>
                                    <td width="60%">
                                        <table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;">
                                            <tr>
                                                <td class="a4-meta-label">Nomor</td>
                                                <td class="a4-meta-colon">:</td>
                                                <td><span id="a4Number">[DRAF — Menunggu Penomoran]</span></td>
                                            </tr>
                                            <tr>
                                                <td class="a4-meta-label">Sifat</td>
                                                <td class="a4-meta-colon">:</td>
                                                <td><span id="a4Confidentiality">Biasa</span></td>
                                            </tr>
                                            <tr>
                                                <td class="a4-meta-label">Lampiran</td>
                                                <td class="a4-meta-colon">:</td>
                                                <td><span id="a4Attachment">-</span></td>
                                            </tr>
                                            <tr>
                                                <td class="a4-meta-label">Perihal</td>
                                                <td class="a4-meta-colon">:</td>
                                                <td><strong id="a4Title">Penugasan Tim Audit Infrastruktur TI & Keamanan Siber</strong></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="40%" align="right" valign="top">
                                        <div id="a4DateCity">Ponorogo, <?= date('d F Y') ?></div>
                                        <div style="text-align:left;display:inline-block;margin-top:14px;">
                                            <div>Kepada Yth.</div>
                                            <div id="a4Recipient" style="font-weight:normal;white-space:pre-line;">Yth. Seluruh Pejabat Struktural dan Tim Teknis LPSI&#10;di Tempat</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- SALAM PEMBUKA & PARAGRAF PEMBUKA -->
                            <div class="a4-opening" id="a4GreetingOpening">Dengan hormat,</div>
                            <div class="a4-body" id="a4Opening">Dalam rangka mewujudkan tata kelola teknologi informasi yang andal, aman, dan akuntabel di lingkungan Universitas Muhammadiyah Ponorogo, dengan ini pimpinan menugaskan kepada tim yang tercantum untuk melaksanakan kegiatan audit dan evaluasi infrastruktur sistem informasi.</div>

                            <!-- ISI POKOK SURAT -->
                            <div class="a4-body" id="a4Body">Adapun ketentuan dan ruang lingkup pelaksanaan adalah sebagai berikut:&#10;1. Melakukan pemeriksaan menyeluruh terhadap server database, jaringan internal, dan sistem cadangan data (backup recovery).&#10;2. Mengidentifikasi kerentanan sistem dan menyusun rekomendasi mitigasi risiko keamanan siber.&#10;3. Menyusun laporan hasil pelaksanaan tugas dan menyampaikannya kepada Rektor paling lambat 7 (tujuh) hari kerja setelah penugasan selesai.</div>

                            <!-- PARAGRAF PENUTUP & SALAM PENUTUP -->
                            <div class="a4-closing" id="a4Closing">Demikian surat tugas ini diterbitkan untuk dilaksanakan dengan penuh rasa tanggung jawab dan dedikasi.</div>
                            <div class="a4-opening" id="a4GreetingClosing" style="margin-bottom:12px;">Hormat kami,</div>

                            <!-- TANDA TANGAN & CAP -->
                            <div class="a4-sign-wrap">
                                <div class="a4-sign-box">
                                    <div class="a4-sign-position" id="a4SignPosition">Kepala LPSI</div>
                                    <div class="a4-sign-middle" id="a4SignMiddle">
                                        <img src="" class="a4-signature-img" id="a4SignImg" style="display:none;" alt="Tanda Tangan">
                                        <img src="" class="a4-stamp-img a4-stamp-left" id="a4StampImg" style="display:none;" alt="Cap Stempel">
                                    </div>
                                    <div class="a4-sign-name" id="a4SignName">Ahmad Fauzi, M.Kom.</div>
                                    <div class="a4-sign-nip" id="a4SignNip">NIDN. 0712058801</div>
                                </div>
                            </div>

                            <!-- TEMBUSAN -->
                            <div class="a4-copies" id="a4CopiesWrap">
                                <strong><u>Tembusan:</u></strong>
                                <div id="a4Copies" style="white-space:pre-line;margin-top:2px;">1. Rektor Universitas Muhammadiyah Ponorogo (sebagai laporan)&#10;2. Wakil Rektor Terkait&#10;3. Arsip LPSI</div>
                            </div>

                            <!-- FOOTER NOTE -->
                            <div class="a4-footer-note" id="a4FooterNoteWrap">
                                <span id="a4FooterNote">Dokumen resmi diterbitkan melalui SIMANTAP · Universitas Muhammadiyah Ponorogo</span>
                                <span>Halaman 1/1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

