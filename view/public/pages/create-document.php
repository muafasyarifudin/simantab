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
            'heading_size' => $_POST['heading_size'] ?? '14pt',
            'kop_align' => $_POST['kop_align'] ?? 'center',
            'kop_line_style' => $_POST['kop_line_style'] ?? 'double',
            'kop_line_weight' => $_POST['kop_line_weight'] ?? 'normal',
            'kop_spacing' => $_POST['kop_spacing'] ?? 'normal',
            'kop_inst1_size' => $_POST['kop_inst1_size'] ?? '14pt',
            'kop_inst2_size' => $_POST['kop_inst2_size'] ?? '12pt',
            'kop_address_size' => $_POST['kop_address_size'] ?? '9pt',
            'kop_contact_size' => $_POST['kop_contact_size'] ?? '8.5pt',
            'logo_align' => $_POST['logo_align'] ?? 'left',
            'logo_size' => $_POST['logo_size'] ?? 'medium',
            'font_family' => $_POST['font_family'] ?? 'serif',
            'paper_margin' => $_POST['paper_margin'] ?? 'normal',
            'custom_number' => $customNumber ?: '',
            'confidentiality' => $_POST['confidentiality'] ?? 'Biasa',
            'attachment' => trim((string)($_POST['attachment'] ?? '-')),
            'title' => trim((string)($_POST['title'] ?? '')),
            'meta_font_size' => $_POST['meta_font_size'] ?? '11pt',
            'date_size' => $_POST['date_size'] ?? '11pt',
            'recipient' => trim((string)($_POST['recipient'] ?? '')),
            'recipient_size' => $_POST['recipient_size'] ?? '11pt',
            'greeting_opening' => trim((string)($_POST['greeting_opening'] ?? 'Dengan hormat,')),
            'greeting_opening_size' => $_POST['greeting_opening_size'] ?? '11.5pt',
            'opening' => trim((string)($_POST['opening'] ?? '')),
            'opening_size' => $_POST['opening_size'] ?? '11.5pt',
            'body' => trim((string)($_POST['body'] ?? '')),
            'body_size' => $_POST['body_size'] ?? '11.5pt',
            'body_line_height' => $_POST['body_line_height'] ?? '1.6',
            'paragraph_spacing' => $_POST['paragraph_spacing'] ?? 'normal',
            'closing' => trim((string)($_POST['closing'] ?? '')),
            'closing_size' => $_POST['closing_size'] ?? '11.5pt',
            'greeting_closing' => trim((string)($_POST['greeting_closing'] ?? 'Hormat kami,')),
            'greeting_closing_size' => $_POST['greeting_closing_size'] ?? '11.5pt',
            'city' => trim((string)($_POST['city'] ?? $defaultCity)),
            'date' => $_POST['document_date'] ?? $defaultDate,
            'signer_position' => trim((string)($_POST['signer_position'] ?? 'Kepala LPSI')),
            'signer_position_size' => $_POST['signer_position_size'] ?? '11.5pt',
            'signer_name' => trim((string)($_POST['signer_name'] ?? $defaultSignerName)),
            'signer_name_size' => $_POST['signer_name_size'] ?? '12pt',
            'signer_number' => trim((string)($_POST['signer_number'] ?? 'NIDN. 0712058801')),
            'signer_number_size' => $_POST['signer_number_size'] ?? '10pt',
            'signature_type' => $_POST['signature_type'] ?? 'image',
            'signature_size' => $_POST['signature_size'] ?? 'medium',
            'signature_space_height' => $_POST['signature_space_height'] ?? '80px',
            'stamp_position' => $_POST['stamp_position'] ?? 'left',
            'stamp_size' => $_POST['stamp_size'] ?? 'medium',
            'stamp_opacity' => $_POST['stamp_opacity'] ?? '85',
            'copies' => trim((string)($_POST['copies'] ?? '')),
            'copies_size' => $_POST['copies_size'] ?? '10pt',
            'footer_note' => trim((string)($_POST['footer_note'] ?? '')),
            'footer_size' => $_POST['footer_size'] ?? '8.5pt',
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
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <!-- Auto Save Status Badge -->
            <div id="autoSaveStatus" class="autosave-badge saved">
                <i class="ri-check-double-line"></i>
                <span id="autoSaveText">Draf tersimpan otomatis</span>
                <small id="autoSaveTime" style="opacity:0.8;font-size:10.5px;"></small>
            </div>

            <div class="builder-view-toggle">
                <button type="button" class="active" data-view-mode="split" onclick="window.setWorkViewMode('split'); return false;"><i class="ri-layout-column-line"></i>Split Workbench</button>
                <button type="button" data-view-mode="form" onclick="window.setWorkViewMode('form'); return false;"><i class="ri-edit-box-line"></i>Form Saja</button>
                <button type="button" data-view-mode="preview" onclick="window.setWorkViewMode('preview'); return false;"><i class="ri-file-paper-line"></i>Kertas A4</button>
            </div>
        </div>
    </div>

    <!-- Draft Recovery Alert Banner (Muncul otomatis jika ada draf tersimpan dari sesi sebelumnya) -->
    <div id="draftRecoveryAlert" class="draft-recovery-alert" style="display:none;">
        <div class="draft-recovery-info">
            <i class="ri-history-line"></i>
            <div>
                <strong>Draf Tersimpan Otomatis Ditemukan!</strong>
                <p style="margin:2px 0 0;font-size:11.5px;color:var(--muted);" id="draftRecoveryMsg">Ditemukan draf yang belum disimpan dari sesi sebelumnya.</p>
            </div>
        </div>
        <div class="draft-recovery-actions">
            <button type="button" class="btn-restore-draft" id="btnRestoreDraft"><i class="ri-restart-line"></i> Pulihkan Draf</button>
            <button type="button" class="btn-discard-draft" id="btnDiscardDraft"><i class="ri-delete-bin-line"></i> Buang Draf</button>
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
                                <p>Tentukan jenis surat, nomor resmi, perihal dokumen, dan ukuran font metadata.</p>
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
                                <div class="field-header-flex">
                                    <label>Judul Heading Surat</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="heading_size" id="heading_size" class="size-select-sm">
                                            <option value="12pt">12 pt</option>
                                            <option value="13pt">13 pt</option>
                                            <option value="14pt" selected>14 pt (Standar)</option>
                                            <option value="15pt">15 pt</option>
                                            <option value="16pt">16 pt</option>
                                            <option value="18pt">18 pt</option>
                                        </select>
                                    </div>
                                </div>
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
                                <div class="field-header-flex">
                                    <label>Ukuran Font Metadata (Nomor, Sifat, Hal)</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="meta_font_size" id="meta_font_size" class="size-select-sm">
                                            <option value="9.5pt">9.5 pt</option>
                                            <option value="10pt">10 pt</option>
                                            <option value="10.5pt">10.5 pt</option>
                                            <option value="11pt" selected>11 pt (Standar)</option>
                                            <option value="11.5pt">11.5 pt</option>
                                            <option value="12pt">12 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="attachment" id="attachment" value="-" placeholder="Lampiran: - atau 1 Berkas">
                            </div>

                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Tanggal Surat <b>*</b></label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="date_size" id="date_size" class="size-select-sm">
                                            <option value="9.5pt">9.5 pt</option>
                                            <option value="10pt">10 pt</option>
                                            <option value="10.5pt">10.5 pt</option>
                                            <option value="11pt" selected>11 pt</option>
                                            <option value="12pt">12 pt</option>
                                        </select>
                                    </div>
                                </div>
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
                                <p>Kustomisasi teks KOP, ukuran font tiap baris, perataan, dan margin kertas.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Nama Lembaga Induk / Instansi Atas (Baris 1) <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_inst1_size" id="kop_inst1_size" class="size-select-sm">
                                        <option value="11pt">11 pt</option>
                                        <option value="12pt">12 pt</option>
                                        <option value="13pt">13 pt</option>
                                        <option value="14pt" selected>14 pt (Standar)</option>
                                        <option value="15pt">15 pt</option>
                                        <option value="16pt">16 pt</option>
                                        <option value="18pt">18 pt</option>
                                        <option value="20pt">20 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="institution" id="kop_inst1" value="<?= e($defaultInst1) ?>" required>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Nama Satuan Kerja / Unit / Fakultas (Baris 2)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_inst2_size" id="kop_inst2_size" class="size-select-sm">
                                        <option value="10pt">10 pt</option>
                                        <option value="11pt">11 pt</option>
                                        <option value="12pt" selected>12 pt (Standar)</option>
                                        <option value="13pt">13 pt</option>
                                        <option value="14pt">14 pt</option>
                                        <option value="16pt">16 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="unit" id="kop_inst2" value="<?= e($defaultUnit) ?>">
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Alamat Lengkap KOP <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_address_size" id="kop_address_size" class="size-select-sm">
                                        <option value="7.5pt">7.5 pt</option>
                                        <option value="8pt">8 pt</option>
                                        <option value="8.5pt">8.5 pt</option>
                                        <option value="9pt" selected>9 pt (Standar)</option>
                                        <option value="9.5pt">9.5 pt</option>
                                        <option value="10pt">10 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="address" id="kop_address" rows="2" required><?= e($defaultAddress) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Kontak KOP (Telepon, Email, Website)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_contact_size" id="kop_contact_size" class="size-select-sm">
                                        <option value="7pt">7 pt</option>
                                        <option value="7.5pt">7.5 pt</option>
                                        <option value="8pt">8 pt</option>
                                        <option value="8.5pt" selected>8.5 pt (Standar)</option>
                                        <option value="9pt">9 pt</option>
                                        <option value="10pt">10 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="contact" id="kop_contact" value="<?= e($defaultContact) ?>">
                        </div>

                        <div class="builder-grid-3">
                            <div class="builder-field">
                                <label>Perataan KOP (Alignment)</label>
                                <select name="kop_align" id="kop_align">
                                    <option value="center" selected>Tengah (Standar)</option>
                                    <option value="left">Rata Kiri</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Gaya Huruf (Font Family)</label>
                                <select name="font_family" id="font_family">
                                    <option value="serif" selected>Serif (Times Formal)</option>
                                    <option value="sans">Sans-Serif (Modern)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Margin Lembar Kertas A4</label>
                                <select name="paper_margin" id="paper_margin">
                                    <option value="compact">Ringkas (1.8 cm)</option>
                                    <option value="normal" selected>Standar Dinas (2.5 cm)</option>
                                    <option value="spacious">Lebar / Luas (3.2 cm)</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- LOGO & GARIS PEMBATAS KOP -->
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon emerald"><i class="ri-image-line"></i></span>
                            <div>
                                <h3>Logo, Garis Pembatas & Spasi KOP</h3>
                                <p>Unggah lambang resmi, atur ukuran logo, jarak spasi, dan ketebalan garis.</p>
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
                                <label>Ukuran Dimensi Logo</label>
                                <select name="logo_size" id="logo_size">
                                    <option value="small">Kecil (56 px)</option>
                                    <option value="medium" selected>Sedang (72 px)</option>
                                    <option value="large">Besar (88 px)</option>
                                    <option value="xlarge">Ekstra Besar (104 px)</option>
                                </select>
                            </div>
                        </div>

                        <div class="builder-grid-2" style="margin-top:6px;">
                            <div class="builder-field">
                                <label>Jarak Spasi KOP ke Garis</label>
                                <select name="kop_spacing" id="kop_spacing">
                                    <option value="compact">Rapat (2 mm)</option>
                                    <option value="normal" selected>Normal (6 mm)</option>
                                    <option value="spacious">Longgar (12 mm)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Ketebalan Garis KOP</label>
                                <select name="kop_line_weight" id="kop_line_weight">
                                    <option value="thin">Tipis (1.5 px)</option>
                                    <option value="normal" selected>Standar (3 px)</option>
                                    <option value="thick">Tebal Tegas (4.5 px)</option>
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
                            <div class="field-header-flex">
                                <label>Tujuan / Penerima Surat (Kepada Yth.) <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="recipient_size" id="recipient_size" class="size-select-sm">
                                        <option value="9.5pt">9.5 pt</option>
                                        <option value="10pt">10 pt</option>
                                        <option value="10.5pt">10.5 pt</option>
                                        <option value="11pt" selected>11 pt (Standar)</option>
                                        <option value="11.5pt">11.5 pt</option>
                                        <option value="12pt">12 pt</option>
                                        <option value="13pt">13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="recipient" id="recipient" rows="3" required placeholder="Contoh:&#10;Yth. Dekan Fakultas Teknik&#10;Universitas Muhammadiyah Ponorogo&#10;di Tempat">Yth. Seluruh Pejabat Struktural dan Tim Teknis LPSI
Universitas Muhammadiyah Ponorogo
di Tempat</textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Salam Pembuka (Greeting Opening)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="greeting_opening_size" id="greeting_opening_size" class="size-select-sm">
                                        <option value="10pt">10 pt</option>
                                        <option value="10.5pt">10.5 pt</option>
                                        <option value="11pt">11 pt</option>
                                        <option value="11.5pt" selected>11.5 pt (Standar)</option>
                                        <option value="12pt">12 pt</option>
                                        <option value="13pt">13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <div class="pill-selector" style="margin-bottom:8px;">
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Dengan hormat,'); return false;">Dengan hormat,</button>
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Assalamu\'alaikum Wr. Wb.,'); return false;">Assalamu'alaikum Wr. Wb.,</button>
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Salam sejahtera,'); return false;">Salam sejahtera,</button>
                                <button type="button" class="pill-opt" onclick="window.setOpeningGreeting('Selamat pagi/siang,'); return false;">Selamat pagi,</button>
                            </div>
                            <input type="text" name="greeting_opening" id="greeting_opening" value="Dengan hormat,">
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Paragraf Pembuka / Dasar Penerbitan Surat</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="opening_size" id="opening_size" class="size-select-sm">
                                        <option value="10pt">10 pt</option>
                                        <option value="10.5pt">10.5 pt</option>
                                        <option value="11pt">11 pt</option>
                                        <option value="11.5pt" selected>11.5 pt (Standar)</option>
                                        <option value="12pt">12 pt</option>
                                        <option value="13pt">13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="opening" id="opening" rows="3" placeholder="Contoh: Dalam rangka pelaksanaan audit kepatuhan infrastruktur sistem informasi semester ganjil tahun akademik 2026/2027, bersama surat ini pimpinan menugaskan:">Dalam rangka mewujudkan tata kelola teknologi informasi yang andal, aman, dan akuntabel di lingkungan Universitas Muhammadiyah Ponorogo, dengan ini pimpinan menugaskan kepada tim yang tercantum untuk melaksanakan kegiatan audit dan evaluasi infrastruktur sistem informasi.</textarea>
                        </div>
                    </section>

                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-file-list-3-line"></i></span>
                            <div>
                                <h3>Pokok Isi Surat, Spasi & Penutup</h3>
                                <p>Rincian isi surat, ukuran teks, jarak spasi baris (line height), dan penutup.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2" style="margin-bottom:12px;">
                            <div class="builder-field">
                                <label>Ukuran Font Isi Pokok Surat</label>
                                <select name="body_size" id="body_size">
                                    <option value="9.5pt">9.5 pt</option>
                                    <option value="10pt">10 pt</option>
                                    <option value="10.5pt">10.5 pt</option>
                                    <option value="11pt">11 pt</option>
                                    <option value="11.5pt" selected>11.5 pt (Standar Dinas)</option>
                                    <option value="12pt">12 pt</option>
                                    <option value="13pt">13 pt</option>
                                    <option value="14pt">14 pt</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Jarak Spasi Baris (Line Height)</label>
                                <select name="body_line_height" id="body_line_height">
                                    <option value="1.2">Sangat Rapat (1.2)</option>
                                    <option value="1.4">Rapat (1.4)</option>
                                    <option value="1.6" selected>Standar Dinas (1.6 ~ 1.5 spasi)</option>
                                    <option value="1.8">Longgar (1.8)</option>
                                    <option value="2.0">Ganda (2.0)</option>
                                </select>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Isi Dokumen / Rincian Penugasan <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Spasi Paragraf:</span>
                                    <select name="paragraph_spacing" id="paragraph_spacing" class="size-select-sm">
                                        <option value="compact">Rapat (6px)</option>
                                        <option value="normal" selected>Normal (10px)</option>
                                        <option value="relaxed">Longgar (16px)</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="body" id="body" rows="8" required placeholder="Tuliskan isi surat lengkap di sini...">Adapun ketentuan dan ruang lingkup pelaksanaan adalah sebagai berikut:
1. Melakukan pemeriksaan menyeluruh terhadap server database, jaringan internal, dan sistem cadangan data (backup recovery).
2. Mengidentifikasi kerentanan sistem dan menyusun rekomendasi mitigasi risiko keamanan siber.
3. Menyusun laporan hasil pelaksanaan tugas dan menyampaikannya kepada Rektor paling lambat 7 (tujuh) hari kerja setelah penugasan selesai.</textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Paragraf Penutup (Closing Statement)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="closing_size" id="closing_size" class="size-select-sm">
                                        <option value="10pt">10 pt</option>
                                        <option value="10.5pt">10.5 pt</option>
                                        <option value="11pt">11 pt</option>
                                        <option value="11.5pt" selected>11.5 pt (Standar)</option>
                                        <option value="12pt">12 pt</option>
                                        <option value="13pt">13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="closing" id="closing" rows="2" placeholder="Contoh: Demikian surat tugas ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh tanggung jawab.">Demikian surat tugas ini diterbitkan untuk dilaksanakan dengan penuh rasa tanggung jawab dan dedikasi.</textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Salam Penutup</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="greeting_closing_size" id="greeting_closing_size" class="size-select-sm">
                                        <option value="10pt">10 pt</option>
                                        <option value="10.5pt">10.5 pt</option>
                                        <option value="11pt">11 pt</option>
                                        <option value="11.5pt" selected>11.5 pt (Standar)</option>
                                        <option value="12pt">12 pt</option>
                                        <option value="13pt">13 pt</option>
                                    </select>
                                </div>
                            </div>
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
                                <h3>Pejabat & Ruang Tanda Tangan</h3>
                                <p>Identitas penandatangan, ukuran font, tinggi ruang tanda tangan & gambar scan.</p>
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
                                <div class="field-header-flex">
                                    <label>Jabatan Penandatangan <b>*</b></label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="signer_position_size" id="signer_position_size" class="size-select-sm">
                                            <option value="10pt">10 pt</option>
                                            <option value="10.5pt">10.5 pt</option>
                                            <option value="11pt">11 pt</option>
                                            <option value="11.5pt" selected>11.5 pt (Standar)</option>
                                            <option value="12pt">12 pt</option>
                                            <option value="13pt">13 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="signer_position" id="signer_position" value="Kepala LPSI" required>
                            </div>

                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Nama Lengkap Pejabat <b>*</b></label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="signer_name_size" id="signer_name_size" class="size-select-sm">
                                            <option value="10.5pt">10.5 pt</option>
                                            <option value="11pt">11 pt</option>
                                            <option value="11.5pt">11.5 pt</option>
                                            <option value="12pt" selected>12 pt (Standar)</option>
                                            <option value="13pt">13 pt</option>
                                            <option value="14pt">14 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="signer_name" id="signer_name" value="<?= e($defaultSignerName) ?>" required>
                            </div>

                            <div class="builder-field full">
                                <div class="field-header-flex">
                                    <label>NIP / NIDN / NBM Pejabat</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="signer_number_size" id="signer_number_size" class="size-select-sm">
                                            <option value="8.5pt">8.5 pt</option>
                                            <option value="9pt">9 pt</option>
                                            <option value="9.5pt">9.5 pt</option>
                                            <option value="10pt" selected>10 pt (Standar)</option>
                                            <option value="10.5pt">10.5 pt</option>
                                            <option value="11pt">11 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="signer_number" id="signer_number" value="NIDN. 0712058801">
                            </div>
                        </div>

                        <div class="builder-grid-2" style="margin-top:8px;">
                            <div class="builder-field">
                                <label>Tinggi Ruang Tanda Tangan</label>
                                <select name="signature_space_height" id="signature_space_height">
                                    <option value="45px">Kecil / Ringkas (45 px)</option>
                                    <option value="65px">Sedang (65 px)</option>
                                    <option value="80px" selected>Standar Resmi (80 px)</option>
                                    <option value="100px">Tinggi (100 px)</option>
                                    <option value="120px">Ekstra Tinggi (120 px)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Ukuran Gambar Tanda Tangan</label>
                                <select name="signature_size" id="signature_size">
                                    <option value="small">Kecil (50 px)</option>
                                    <option value="medium" selected>Sedang (70 px)</option>
                                    <option value="large">Besar (90 px)</option>
                                    <option value="xlarge">Ekstra Besar (110 px)</option>
                                </select>
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
                                <p>Atur ukuran stempel dinas, opasitas tinta basah, dan penempatan tumpang tindih.</p>
                            </div>
                        </div>

                        <!-- Upload Cap -->
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

                        <div class="builder-grid-3">
                            <div class="builder-field">
                                <label>Posisi Stempel</label>
                                <select name="stamp_position" id="stamp_position">
                                    <option value="left" selected>Kiri Menimpa TTD</option>
                                    <option value="center">Tengah Tepat di TTD</option>
                                    <option value="right">Kanan TTD</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Ukuran Diameter Cap</label>
                                <select name="stamp_size" id="stamp_size">
                                    <option value="small">Kecil (65 px)</option>
                                    <option value="medium" selected>Sedang (85 px)</option>
                                    <option value="large">Besar (105 px)</option>
                                    <option value="xlarge">Ekstra Besar (125 px)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Opasitas (Ketebalan Tinta)</label>
                                <select name="stamp_opacity" id="stamp_opacity">
                                    <option value="100">100% (Pekat)</option>
                                    <option value="85" selected>85% (Tinta Basah)</option>
                                    <option value="70">70% (Halus)</option>
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
                                <p>Daftar tembusan surat, ukuran font, dan catatan kaki dokumen.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Tembusan Surat (Satu baris per penerima)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="copies_size" id="copies_size" class="size-select-sm">
                                        <option value="8.5pt">8.5 pt</option>
                                        <option value="9pt">9 pt</option>
                                        <option value="9.5pt">9.5 pt</option>
                                        <option value="10pt" selected>10 pt (Standar)</option>
                                        <option value="10.5pt">10.5 pt</option>
                                        <option value="11pt">11 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="copies" id="copies" rows="4" placeholder="Contoh:&#10;1. Rektor Universitas Muhammadiyah Ponorogo&#10;2. Wakil Rektor II Bidang Administrasi dan Keuangan&#10;3. Arsip">1. Rektor Universitas Muhammadiyah Ponorogo (sebagai laporan)
2. Wakil Rektor Terkait
3. Arsip LPSI</textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Catatan Kaki Dokumen (Footer Note)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="footer_size" id="footer_size" class="size-select-sm">
                                        <option value="7.5pt">7.5 pt</option>
                                        <option value="8pt">8 pt</option>
                                        <option value="8.5pt" selected>8.5 pt (Standar)</option>
                                        <option value="9pt">9 pt</option>
                                        <option value="9.5pt">9.5 pt</option>
                                        <option value="10pt">10 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="footer_note" id="footer_note" value="Dokumen resmi diterbitkan melalui SIMANTAP · Universitas Muhammadiyah Ponorogo" placeholder="Contoh: Dokumen ini telah diverifikasi elektronik melalui SIMANTAP">
                        </div>
                    </section>
                </div>

                <!-- Tombol Navigasi Wizard & Simpan -->
                <div class="builder-actions-bar">
                    <a class="secondary-button" href="?page=documents"><i class="ri-close-line"></i>Batal</a>
                    <div style="display:flex;gap:8px;align-items:center;">
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

