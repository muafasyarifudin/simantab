<?php
$id = (int)($_GET['id'] ?? 0);
$d = DocumentService::find($id);
RbacService::authorize('documents.update', (int)$d['owner_unit_id']);

if (!in_array($d['status'], ['draft', 'revision'], true)) {
    echo '<div class="page-shell"><div class="auth-error">Dokumen final tidak dapat diubah. Buat draf baru untuk perubahan format.</div></div>';
    return;
}

$v = LetterService::content($d);
$error = '';
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Security::verifyCsrf($_POST['_token'] ?? null);

        $v['institution'] = trim((string)($_POST['institution'] ?? $v['institution']));
        $v['unit'] = trim((string)($_POST['unit'] ?? $v['unit']));
        $v['address'] = trim((string)($_POST['address'] ?? $v['address']));
        $v['contact'] = trim((string)($_POST['contact'] ?? $v['contact']));
        $v['heading'] = trim((string)($_POST['heading'] ?? $v['heading']));
        $v['heading_size'] = $_POST['heading_size'] ?? ($v['heading_size'] ?? '14pt');
        $v['kop_align'] = $_POST['kop_align'] ?? ($v['kop_align'] ?? 'center');
        $v['kop_line_style'] = $_POST['kop_line_style'] ?? ($v['kop_line_style'] ?? 'double');
        $v['kop_line_weight'] = $_POST['kop_line_weight'] ?? ($v['kop_line_weight'] ?? 'normal');
        $v['kop_spacing'] = $_POST['kop_spacing'] ?? ($v['kop_spacing'] ?? 'normal');
        $v['kop_inst1_size'] = $_POST['kop_inst1_size'] ?? ($v['kop_inst1_size'] ?? '14pt');
        $v['kop_inst2_size'] = $_POST['kop_inst2_size'] ?? ($v['kop_inst2_size'] ?? '12pt');
        $v['kop_address_size'] = $_POST['kop_address_size'] ?? ($v['kop_address_size'] ?? '9pt');
        $v['kop_contact_size'] = $_POST['kop_contact_size'] ?? ($v['kop_contact_size'] ?? '8.5pt');
        $v['logo_align'] = $_POST['logo_align'] ?? ($v['logo_align'] ?? 'left');
        $v['logo_size'] = $_POST['logo_size'] ?? ($v['logo_size'] ?? 'medium');
        $v['font_family'] = $_POST['font_family'] ?? ($v['font_family'] ?? 'serif');
        $v['paper_margin'] = $_POST['paper_margin'] ?? ($v['paper_margin'] ?? 'normal');
        $v['custom_number'] = trim((string)($_POST['custom_number'] ?? ''));
        $v['confidentiality'] = $_POST['confidentiality'] ?? ($v['confidentiality'] ?? 'Biasa');
        $v['attachment'] = trim((string)($_POST['attachment'] ?? ($v['attachment'] ?? '-')));
        $v['title'] = trim((string)($_POST['title'] ?? $d['title']));
        $v['meta_font_size'] = $_POST['meta_font_size'] ?? ($v['meta_font_size'] ?? '11pt');
        $v['date_size'] = $_POST['date_size'] ?? ($v['date_size'] ?? '11pt');
        $v['recipient'] = trim((string)($_POST['recipient'] ?? ''));
        $v['recipient_size'] = $_POST['recipient_size'] ?? ($v['recipient_size'] ?? '11pt');
        $v['greeting_opening'] = trim((string)($_POST['greeting_opening'] ?? ($v['greeting_opening'] ?? 'Dengan hormat,')));
        $v['greeting_opening_size'] = $_POST['greeting_opening_size'] ?? ($v['greeting_opening_size'] ?? '11.5pt');
        $v['opening'] = trim((string)($_POST['opening'] ?? ''));
        $v['opening_size'] = $_POST['opening_size'] ?? ($v['opening_size'] ?? '11.5pt');
        $v['body'] = trim((string)($_POST['body'] ?? ''));
        $v['body_size'] = $_POST['body_size'] ?? ($v['body_size'] ?? '11.5pt');
        $v['body_line_height'] = $_POST['body_line_height'] ?? ($v['body_line_height'] ?? '1.6');
        $v['paragraph_spacing'] = $_POST['paragraph_spacing'] ?? ($v['paragraph_spacing'] ?? 'normal');
        $v['closing'] = trim((string)($_POST['closing'] ?? ''));
        $v['closing_size'] = $_POST['closing_size'] ?? ($v['closing_size'] ?? '11.5pt');
        $v['greeting_closing'] = trim((string)($_POST['greeting_closing'] ?? ($v['greeting_closing'] ?? 'Hormat kami,')));
        $v['greeting_closing_size'] = $_POST['greeting_closing_size'] ?? ($v['greeting_closing_size'] ?? '11.5pt');
        $v['city'] = trim((string)($_POST['city'] ?? ''));
        $v['date'] = $_POST['document_date'] ?? date('Y-m-d');
        $v['signer_position'] = trim((string)($_POST['signer_position'] ?? ''));
        $v['signer_position_size'] = $_POST['signer_position_size'] ?? ($v['signer_position_size'] ?? '11.5pt');
        $v['signer_name'] = trim((string)($_POST['signer_name'] ?? ''));
        $v['signer_name_size'] = $_POST['signer_name_size'] ?? ($v['signer_name_size'] ?? '12pt');
        $v['signer_number'] = trim((string)($_POST['signer_number'] ?? ''));
        $v['signer_number_size'] = $_POST['signer_number_size'] ?? ($v['signer_number_size'] ?? '10pt');
        $v['signature_size'] = $_POST['signature_size'] ?? ($v['signature_size'] ?? 'medium');
        $v['signature_space_height'] = $_POST['signature_space_height'] ?? ($v['signature_space_height'] ?? '80px');
        $v['stamp_position'] = $_POST['stamp_position'] ?? ($v['stamp_position'] ?? 'left');
        $v['stamp_size'] = $_POST['stamp_size'] ?? ($v['stamp_size'] ?? 'medium');
        $v['stamp_opacity'] = $_POST['stamp_opacity'] ?? ($v['stamp_opacity'] ?? '85');
        $v['copies'] = trim((string)($_POST['copies'] ?? ''));
        $v['copies_size'] = $_POST['copies_size'] ?? ($v['copies_size'] ?? '10pt');
        $v['footer_note'] = trim((string)($_POST['footer_note'] ?? ''));
        $v['footer_size'] = $_POST['footer_size'] ?? ($v['footer_size'] ?? '8.5pt');

        // Handle base64 from client or fallback files
        if (!empty($_POST['logo_base64'])) $v['logo'] = LetterService::saveBase64Image($_POST['logo_base64'], 'logo');
        if (isset($_POST['remove_logo'])) $v['logo'] = '';
        if (empty($v['logo']) && isset($_FILES['logo_file']) && ($_FILES['logo_file']['error'] ?? 4) === 0) {
            $v['logo'] = LetterService::upload($_FILES['logo_file'], 'logo');
        }

        if (!empty($_POST['signature_base64'])) $v['signature'] = LetterService::saveBase64Image($_POST['signature_base64'], 'sign');
        if (isset($_POST['remove_signature'])) $v['signature'] = '';
        if (empty($v['signature']) && isset($_FILES['signature_file']) && ($_FILES['signature_file']['error'] ?? 4) === 0) {
            $v['signature'] = LetterService::upload($_FILES['signature_file'], 'sign');
        }

        if (!empty($_POST['stamp_base64'])) $v['stamp'] = LetterService::saveBase64Image($_POST['stamp_base64'], 'stamp');
        if (isset($_POST['remove_stamp'])) $v['stamp'] = '';
        if (empty($v['stamp']) && isset($_FILES['stamp_file']) && ($_FILES['stamp_file']['error'] ?? 4) === 0) {
            $v['stamp'] = LetterService::upload($_FILES['stamp_file'], 'stamp');
        }

        $q = Database::connection()->prepare('SELECT content_json FROM document_versions WHERE document_id=? ORDER BY version_no DESC LIMIT 1');
        $q->execute([$id]);
        $content = json_decode($q->fetchColumn() ?: '{}', true) ?: [];
        $content['letter'] = $v;

        DocumentService::update($id, [
            'title' => $v['title'],
            'summary' => $v['body'],
            'number' => $v['custom_number'] ?: null,
            'content' => $content,
            'change_summary' => 'Memperbarui kustomisasi format surat'
        ]);

        $saved = true;
        // Refresh
        $d = DocumentService::find($id);
        $v = LetterService::content($d);
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
    }
}
?>

<div class="page-shell full-width builder-shell">
    <div class="builder-header">
        <div class="builder-header-left">
            <p class="eyebrow">MANAJEMEN SURAT / SESUAIKAN DRAF</p>
            <h1>Sesuaikan Format Surat<span>.</span></h1>
            <p>Ubah KOP, Logo, Garis Dinas, Identitas Surat, Konten, Tanda Tangan & Cap stempel untuk dokumen #<?= $id ?>.</p>
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
            <a class="secondary-button" href="?page=document-detail&id=<?= $id ?>"><i class="ri-eye-line"></i>Lihat Detail</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="auth-error" role="alert" style="margin-bottom:18px;"><i class="ri-error-warning-line"></i> <?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($saved): ?>
        <div class="auth-success" role="status" style="margin-bottom:18px;background:var(--teal-bg);color:var(--teal);padding:12px 18px;border-radius:10px;font-weight:700;"><i class="ri-checkbox-circle-line"></i> Perubahan format surat berhasil disimpan sebagai versi baru!</div>
    <?php endif; ?>

    <form id="letterBuilderForm" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= e(Security::csrfToken()) ?>">
        <input type="hidden" name="logo_base64" id="logo_base64" value="<?= e($v['logo'] ?? '') ?>">
        <input type="hidden" name="signature_base64" id="signature_base64" value="<?= e($v['signature'] ?? '') ?>">
        <input type="hidden" name="stamp_base64" id="stamp_base64" value="<?= e($v['stamp'] ?? '') ?>">

        <div class="builder-workbench" id="builderWorkbench">
            <!-- KOLOM KIRI: Form Controls -->
            <div class="builder-form-col">
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
                                <h3>Identitas & Nomor Dokumen</h3>
                                <p>Perbarui perihal, nomor resmi, sifat dokumen, dan ukuran font metadata.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field full">
                                <label>Perihal / Hal Surat <b>*</b></label>
                                <input type="text" name="title" id="title" required maxlength="500" value="<?= e($v['title'] ?: $d['title']) ?>">
                            </div>

                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Judul Kategori / Heading Kertas</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="heading_size" id="heading_size" class="size-select-sm">
                                            <option value="12pt" <?= ($v['heading_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                            <option value="13pt" <?= ($v['heading_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                            <option value="14pt" <?= ($v['heading_size'] ?? '14pt') === '14pt' ? 'selected' : '' ?>>14 pt (Standar)</option>
                                            <option value="15pt" <?= ($v['heading_size'] ?? '') === '15pt' ? 'selected' : '' ?>>15 pt</option>
                                            <option value="16pt" <?= ($v['heading_size'] ?? '') === '16pt' ? 'selected' : '' ?>>16 pt</option>
                                            <option value="18pt" <?= ($v['heading_size'] ?? '') === '18pt' ? 'selected' : '' ?>>18 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="heading" id="heading" value="<?= e($v['heading'] ?: strtoupper($d['type_name'])) ?>">
                            </div>

                            <div class="builder-field">
                                <label>Nomor Surat (Kustom/Resmi)</label>
                                <input type="text" name="custom_number" id="custom_number" value="<?= e($v['custom_number'] ?: $d['number']) ?>" placeholder="Nomor surat">
                            </div>

                            <div class="builder-field">
                                <label>Sifat Surat <b>*</b></label>
                                <select name="confidentiality" id="confidentiality" required>
                                    <option value="Biasa" <?= ($v['confidentiality'] ?? '') === 'Biasa' ? 'selected' : '' ?>>Biasa</option>
                                    <option value="Penting" <?= ($v['confidentiality'] ?? '') === 'Penting' ? 'selected' : '' ?>>Penting</option>
                                    <option value="Segera" <?= ($v['confidentiality'] ?? '') === 'Segera' ? 'selected' : '' ?>>Segera</option>
                                    <option value="Rahasia" <?= ($v['confidentiality'] ?? '') === 'Rahasia' ? 'selected' : '' ?>>Rahasia</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Lampiran</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="meta_font_size" id="meta_font_size" class="size-select-sm">
                                            <option value="9.5pt" <?= ($v['meta_font_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                            <option value="10pt" <?= ($v['meta_font_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                            <option value="10.5pt" <?= ($v['meta_font_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                            <option value="11pt" <?= ($v['meta_font_size'] ?? '11pt') === '11pt' ? 'selected' : '' ?>>11 pt (Standar)</option>
                                            <option value="11.5pt" <?= ($v['meta_font_size'] ?? '') === '11.5pt' ? 'selected' : '' ?>>11.5 pt</option>
                                            <option value="12pt" <?= ($v['meta_font_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="attachment" id="attachment" value="<?= e($v['attachment'] ?? '-') ?>">
                            </div>

                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Tanggal Surat</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="date_size" id="date_size" class="size-select-sm">
                                            <option value="9.5pt" <?= ($v['date_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                            <option value="10pt" <?= ($v['date_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                            <option value="10.5pt" <?= ($v['date_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                            <option value="11pt" <?= ($v['date_size'] ?? '11pt') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                            <option value="12pt" <?= ($v['date_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="date" name="document_date" id="document_date" value="<?= e($v['date'] ?? date('Y-m-d')) ?>">
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
                                <p>Sesuaikan nama instansi, alamat, ukuran teks, dan font surat.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Nama Lembaga Induk / Instansi Atas (Baris 1) <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_inst1_size" id="kop_inst1_size" class="size-select-sm">
                                        <option value="11pt" <?= ($v['kop_inst1_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        <option value="12pt" <?= ($v['kop_inst1_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        <option value="13pt" <?= ($v['kop_inst1_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                        <option value="14pt" <?= ($v['kop_inst1_size'] ?? '14pt') === '14pt' ? 'selected' : '' ?>>14 pt (Standar)</option>
                                        <option value="15pt" <?= ($v['kop_inst1_size'] ?? '') === '15pt' ? 'selected' : '' ?>>15 pt</option>
                                        <option value="16pt" <?= ($v['kop_inst1_size'] ?? '') === '16pt' ? 'selected' : '' ?>>16 pt</option>
                                        <option value="18pt" <?= ($v['kop_inst1_size'] ?? '') === '18pt' ? 'selected' : '' ?>>18 pt</option>
                                        <option value="20pt" <?= ($v['kop_inst1_size'] ?? '') === '20pt' ? 'selected' : '' ?>>20 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="institution" id="kop_inst1" value="<?= e($v['institution']) ?>" required>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Nama Satuan Kerja / Unit / Fakultas (Baris 2)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_inst2_size" id="kop_inst2_size" class="size-select-sm">
                                        <option value="10pt" <?= ($v['kop_inst2_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                        <option value="11pt" <?= ($v['kop_inst2_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        <option value="12pt" <?= ($v['kop_inst2_size'] ?? '12pt') === '12pt' ? 'selected' : '' ?>>12 pt (Standar)</option>
                                        <option value="13pt" <?= ($v['kop_inst2_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                        <option value="14pt" <?= ($v['kop_inst2_size'] ?? '') === '14pt' ? 'selected' : '' ?>>14 pt</option>
                                        <option value="16pt" <?= ($v['kop_inst2_size'] ?? '') === '16pt' ? 'selected' : '' ?>>16 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="unit" id="kop_inst2" value="<?= e($v['unit']) ?>">
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Alamat Lengkap KOP <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_address_size" id="kop_address_size" class="size-select-sm">
                                        <option value="7.5pt" <?= ($v['kop_address_size'] ?? '') === '7.5pt' ? 'selected' : '' ?>>7.5 pt</option>
                                        <option value="8pt" <?= ($v['kop_address_size'] ?? '') === '8pt' ? 'selected' : '' ?>>8 pt</option>
                                        <option value="8.5pt" <?= ($v['kop_address_size'] ?? '') === '8.5pt' ? 'selected' : '' ?>>8.5 pt</option>
                                        <option value="9pt" <?= ($v['kop_address_size'] ?? '9pt') === '9pt' ? 'selected' : '' ?>>9 pt (Standar)</option>
                                        <option value="9.5pt" <?= ($v['kop_address_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                        <option value="10pt" <?= ($v['kop_address_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="address" id="kop_address" rows="2" required><?= e($v['address']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Kontak KOP (Telepon, Email, Website)</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="kop_contact_size" id="kop_contact_size" class="size-select-sm">
                                        <option value="7pt" <?= ($v['kop_contact_size'] ?? '') === '7pt' ? 'selected' : '' ?>>7 pt</option>
                                        <option value="7.5pt" <?= ($v['kop_contact_size'] ?? '') === '7.5pt' ? 'selected' : '' ?>>7.5 pt</option>
                                        <option value="8pt" <?= ($v['kop_contact_size'] ?? '') === '8pt' ? 'selected' : '' ?>>8 pt</option>
                                        <option value="8.5pt" <?= ($v['kop_contact_size'] ?? '8.5pt') === '8.5pt' ? 'selected' : '' ?>>8.5 pt (Standar)</option>
                                        <option value="9pt" <?= ($v['kop_contact_size'] ?? '') === '9pt' ? 'selected' : '' ?>>9 pt</option>
                                        <option value="10pt" <?= ($v['kop_contact_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="contact" id="kop_contact" value="<?= e($v['contact']) ?>">
                        </div>

                        <div class="builder-grid-3">
                            <div class="builder-field">
                                <label>Perataan KOP</label>
                                <select name="kop_align" id="kop_align">
                                    <option value="center" <?= ($v['kop_align'] ?? 'center') === 'center' ? 'selected' : '' ?>>Tengah (Standar)</option>
                                    <option value="left" <?= ($v['kop_align'] ?? '') === 'left' ? 'selected' : '' ?>>Rata Kiri</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Gaya Huruf</label>
                                <select name="font_family" id="font_family">
                                    <option value="serif" <?= ($v['font_family'] ?? 'serif') === 'serif' ? 'selected' : '' ?>>Serif (Times Formal)</option>
                                    <option value="sans" <?= ($v['font_family'] ?? '') === 'sans' ? 'selected' : '' ?>>Sans-Serif (Modern)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Margin Lembar Kertas A4</label>
                                <select name="paper_margin" id="paper_margin">
                                    <option value="compact" <?= ($v['paper_margin'] ?? '') === 'compact' ? 'selected' : '' ?>>Ringkas (1.8 cm)</option>
                                    <option value="normal" <?= ($v['paper_margin'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Standar Dinas (2.5 cm)</option>
                                    <option value="spacious" <?= ($v['paper_margin'] ?? '') === 'spacious' ? 'selected' : '' ?>>Lebar / Luas (3.2 cm)</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon emerald"><i class="ri-image-line"></i></span>
                            <div>
                                <h3>Logo & Garis Pembatas KOP</h3>
                                <p>Kelola lambang instansi dan garis pembatas naskah dinas.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Logo Resmi</label>
                            <div class="builder-upload-box">
                                <div class="builder-upload-preview" id="logoPreviewBox">
                                    <?php if (!empty($v['logo'])): ?>
                                        <img src="<?= e($v['logo']) ?>" alt="Logo">
                                    <?php else: ?>
                                        <span class="placeholder-icon"><i class="ri-image-add-line"></i></span>
                                    <?php endif; ?>
                                </div>
                                <div class="builder-upload-info">
                                    <strong>Pilih Logo (PNG / JPG)</strong>
                                    <p>Maksimal 2 MB latar transparan.</p>
                                    <div class="builder-upload-actions">
                                        <input type="file" id="logoFileInput" name="logo_file" accept="image/png,image/jpeg" style="display:none;">
                                        <button type="button" class="secondary-button" onclick="document.getElementById('logoFileInput').click();"><i class="ri-upload-2-line"></i>Ganti Logo</button>
                                        <button type="button" class="secondary-button" id="removeLogoBtn" style="<?= empty($v['logo']) ? 'display:none;' : '' ?>color:var(--rose);"><i class="ri-delete-bin-line"></i>Hapus Logo</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Posisi Logo</label>
                                <select name="logo_align" id="logo_align">
                                    <option value="left" <?= ($v['logo_align'] ?? 'left') === 'left' ? 'selected' : '' ?>>Kiri Saja</option>
                                    <option value="both" <?= ($v['logo_align'] ?? '') === 'both' ? 'selected' : '' ?>>Dua Logo (Kiri & Kanan)</option>
                                    <option value="none" <?= ($v['logo_align'] ?? '') === 'none' ? 'selected' : '' ?>>Tanpa Logo</option>
                                </select>
                            </div>
                            <div class="builder-field">
                                <label>Ukuran Dimensi Logo</label>
                                <select name="logo_size" id="logo_size">
                                    <option value="small" <?= ($v['logo_size'] ?? '') === 'small' ? 'selected' : '' ?>>Kecil (56 px)</option>
                                    <option value="medium" <?= ($v['logo_size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Sedang (72 px)</option>
                                    <option value="large" <?= ($v['logo_size'] ?? '') === 'large' ? 'selected' : '' ?>>Besar (88 px)</option>
                                    <option value="xlarge" <?= ($v['logo_size'] ?? '') === 'xlarge' ? 'selected' : '' ?>>Ekstra Besar (104 px)</option>
                                </select>
                            </div>
                        </div>

                        <div class="builder-grid-2" style="margin-top:6px;">
                            <div class="builder-field">
                                <label>Jarak Spasi KOP ke Garis</label>
                                <select name="kop_spacing" id="kop_spacing">
                                    <option value="compact" <?= ($v['kop_spacing'] ?? '') === 'compact' ? 'selected' : '' ?>>Rapat (2 mm)</option>
                                    <option value="normal" <?= ($v['kop_spacing'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal (6 mm)</option>
                                    <option value="spacious" <?= ($v['kop_spacing'] ?? '') === 'spacious' ? 'selected' : '' ?>>Longgar (12 mm)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Ketebalan Garis KOP</label>
                                <select name="kop_line_weight" id="kop_line_weight">
                                    <option value="thin" <?= ($v['kop_line_weight'] ?? '') === 'thin' ? 'selected' : '' ?>>Tipis (1.5 px)</option>
                                    <option value="normal" <?= ($v['kop_line_weight'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Standar (3 px)</option>
                                    <option value="thick" <?= ($v['kop_line_weight'] ?? '') === 'thick' ? 'selected' : '' ?>>Tebal Tegas (4.5 px)</option>
                                </select>
                            </div>
                        </div>

                        <div class="builder-field full" style="margin-top:10px;">
                            <label>Garis Pembatas KOP <b>*</b></label>
                            <div class="pill-selector">
                                <label class="pill-opt <?= ($v['kop_line_style'] ?? 'double') === 'double' ? 'selected' : '' ?>">
                                    <input type="radio" name="kop_line_style" value="double" <?= ($v['kop_line_style'] ?? 'double') === 'double' ? 'checked' : '' ?>>
                                    <span>Garis Ganda Resmi</span>
                                </label>
                                <label class="pill-opt <?= ($v['kop_line_style'] ?? '') === 'single_thick' ? 'selected' : '' ?>">
                                    <input type="radio" name="kop_line_style" value="single_thick" <?= ($v['kop_line_style'] ?? '') === 'single_thick' ? 'checked' : '' ?>>
                                    <span>Garis Tunggal Tebal</span>
                                </label>
                                <label class="pill-opt <?= ($v['kop_line_style'] ?? '') === 'single_thin' ? 'selected' : '' ?>">
                                    <input type="radio" name="kop_line_style" value="single_thin" <?= ($v['kop_line_style'] ?? '') === 'single_thin' ? 'checked' : '' ?>>
                                    <span>Garis Tunggal Tipis</span>
                                </label>
                                <label class="pill-opt <?= ($v['kop_line_style'] ?? '') === 'dashed' ? 'selected' : '' ?>">
                                    <input type="radio" name="kop_line_style" value="dashed" <?= ($v['kop_line_style'] ?? '') === 'dashed' ? 'checked' : '' ?>>
                                    <span>Garis Putus-putus</span>
                                </label>
                                <label class="pill-opt <?= ($v['kop_line_style'] ?? '') === 'none' ? 'selected' : '' ?>">
                                    <input type="radio" name="kop_line_style" value="none" <?= ($v['kop_line_style'] ?? '') === 'none' ? 'checked' : '' ?>>
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
                                <h3>Penerima & Pembukaan</h3>
                                <p>Tujuan naskah dinas dan salam pembuka.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Tujuan / Penerima Surat (Kepada Yth.) <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="recipient_size" id="recipient_size" class="size-select-sm">
                                        <option value="9.5pt" <?= ($v['recipient_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                        <option value="10pt" <?= ($v['recipient_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                        <option value="10.5pt" <?= ($v['recipient_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                        <option value="11pt" <?= ($v['recipient_size'] ?? '11pt') === '11pt' ? 'selected' : '' ?>>11 pt (Standar)</option>
                                        <option value="11.5pt" <?= ($v['recipient_size'] ?? '') === '11.5pt' ? 'selected' : '' ?>>11.5 pt</option>
                                        <option value="12pt" <?= ($v['recipient_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        <option value="13pt" <?= ($v['recipient_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="recipient" id="recipient" rows="3" required><?= e($v['recipient']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Salam Pembuka</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="greeting_opening_size" id="greeting_opening_size" class="size-select-sm">
                                        <option value="10pt" <?= ($v['greeting_opening_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                        <option value="10.5pt" <?= ($v['greeting_opening_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                        <option value="11pt" <?= ($v['greeting_opening_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        <option value="11.5pt" <?= ($v['greeting_opening_size'] ?? '11.5pt') === '11.5pt' ? 'selected' : '' ?>>11.5 pt (Standar)</option>
                                        <option value="12pt" <?= ($v['greeting_opening_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        <option value="13pt" <?= ($v['greeting_opening_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="greeting_opening" id="greeting_opening" value="<?= e($v['greeting_opening']) ?>">
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Paragraf Pembuka / Dasar Surat</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="opening_size" id="opening_size" class="size-select-sm">
                                        <option value="10pt" <?= ($v['opening_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                        <option value="10.5pt" <?= ($v['opening_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                        <option value="11pt" <?= ($v['opening_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        <option value="11.5pt" <?= ($v['opening_size'] ?? '11.5pt') === '11.5pt' ? 'selected' : '' ?>>11.5 pt (Standar)</option>
                                        <option value="12pt" <?= ($v['opening_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        <option value="13pt" <?= ($v['opening_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="opening" id="opening" rows="3"><?= e($v['opening']) ?></textarea>
                        </div>
                    </section>

                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-file-list-3-line"></i></span>
                            <div>
                                <h3>Isi Pokok & Penutup</h3>
                                <p>Rincian isi surat dan kalimat penutup dinas.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2" style="margin-bottom:12px;">
                            <div class="builder-field">
                                <label>Ukuran Font Isi Pokok Surat</label>
                                <select name="body_size" id="body_size">
                                    <option value="9.5pt" <?= ($v['body_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                    <option value="10pt" <?= ($v['body_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                    <option value="10.5pt" <?= ($v['body_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                    <option value="11pt" <?= ($v['body_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                    <option value="11.5pt" <?= ($v['body_size'] ?? '11.5pt') === '11.5pt' ? 'selected' : '' ?>>11.5 pt (Standar Dinas)</option>
                                    <option value="12pt" <?= ($v['body_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                    <option value="13pt" <?= ($v['body_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                    <option value="14pt" <?= ($v['body_size'] ?? '') === '14pt' ? 'selected' : '' ?>>14 pt</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Jarak Spasi Baris (Line Height)</label>
                                <select name="body_line_height" id="body_line_height">
                                    <option value="1.2" <?= ($v['body_line_height'] ?? '') === '1.2' ? 'selected' : '' ?>>Sangat Rapat (1.2)</option>
                                    <option value="1.4" <?= ($v['body_line_height'] ?? '') === '1.4' ? 'selected' : '' ?>>Rapat (1.4)</option>
                                    <option value="1.6" <?= ($v['body_line_height'] ?? '1.6') === '1.6' ? 'selected' : '' ?>>Standar Dinas (1.6 ~ 1.5 spasi)</option>
                                    <option value="1.8" <?= ($v['body_line_height'] ?? '') === '1.8' ? 'selected' : '' ?>>Longgar (1.8)</option>
                                    <option value="2.0" <?= ($v['body_line_height'] ?? '') === '2.0' ? 'selected' : '' ?>>Ganda (2.0)</option>
                                </select>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Isi Surat Lengkap <b>*</b></label>
                                <div class="size-pill-group">
                                    <span>Spasi Paragraf:</span>
                                    <select name="paragraph_spacing" id="paragraph_spacing" class="size-select-sm">
                                        <option value="compact" <?= ($v['paragraph_spacing'] ?? '') === 'compact' ? 'selected' : '' ?>>Rapat (6px)</option>
                                        <option value="normal" <?= ($v['paragraph_spacing'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal (10px)</option>
                                        <option value="relaxed" <?= ($v['paragraph_spacing'] ?? '') === 'relaxed' ? 'selected' : '' ?>>Longgar (16px)</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="body" id="body" rows="8" required><?= e($v['body']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Paragraf Penutup</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="closing_size" id="closing_size" class="size-select-sm">
                                        <option value="10pt" <?= ($v['closing_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                        <option value="10.5pt" <?= ($v['closing_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                        <option value="11pt" <?= ($v['closing_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        <option value="11.5pt" <?= ($v['closing_size'] ?? '11.5pt') === '11.5pt' ? 'selected' : '' ?>>11.5 pt (Standar)</option>
                                        <option value="12pt" <?= ($v['closing_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        <option value="13pt" <?= ($v['closing_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="closing" id="closing" rows="2"><?= e($v['closing']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Salam Penutup</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="greeting_closing_size" id="greeting_closing_size" class="size-select-sm">
                                        <option value="10pt" <?= ($v['greeting_closing_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                        <option value="10.5pt" <?= ($v['greeting_closing_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                        <option value="11pt" <?= ($v['greeting_closing_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        <option value="11.5pt" <?= ($v['greeting_closing_size'] ?? '11.5pt') === '11.5pt' ? 'selected' : '' ?>>11.5 pt (Standar)</option>
                                        <option value="12pt" <?= ($v['greeting_closing_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                        <option value="13pt" <?= ($v['greeting_closing_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="greeting_closing" id="greeting_closing" value="<?= e($v['greeting_closing']) ?>">
                        </div>
                    </section>
                </div>

                <!-- TAB 4: TANDA TANGAN & CAP -->
                <div class="builder-tab-pane" id="tab-sign" style="display: none;">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon violet"><i class="ri-quill-pen-line"></i></span>
                            <div>
                                <h3>Penandatangan Dokumen</h3>
                                <p>Pejabat berwenang dan tanda tangan basah.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Kota Penetapan</label>
                                <input type="text" name="city" id="city" value="<?= e($v['city']) ?>">
                            </div>
                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Jabatan Penandatangan <b>*</b></label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="signer_position_size" id="signer_position_size" class="size-select-sm">
                                            <option value="10pt" <?= ($v['signer_position_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                            <option value="10.5pt" <?= ($v['signer_position_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                            <option value="11pt" <?= ($v['signer_position_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                            <option value="11.5pt" <?= ($v['signer_position_size'] ?? '11.5pt') === '11.5pt' ? 'selected' : '' ?>>11.5 pt (Standar)</option>
                                            <option value="12pt" <?= ($v['signer_position_size'] ?? '') === '12pt' ? 'selected' : '' ?>>12 pt</option>
                                            <option value="13pt" <?= ($v['signer_position_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="signer_position" id="signer_position" value="<?= e($v['signer_position']) ?>" required>
                            </div>
                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>Nama Lengkap Pejabat <b>*</b></label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="signer_name_size" id="signer_name_size" class="size-select-sm">
                                            <option value="10.5pt" <?= ($v['signer_name_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                            <option value="11pt" <?= ($v['signer_name_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                            <option value="11.5pt" <?= ($v['signer_name_size'] ?? '') === '11.5pt' ? 'selected' : '' ?>>11.5 pt</option>
                                            <option value="12pt" <?= ($v['signer_name_size'] ?? '12pt') === '12pt' ? 'selected' : '' ?>>12 pt (Standar)</option>
                                            <option value="13pt" <?= ($v['signer_name_size'] ?? '') === '13pt' ? 'selected' : '' ?>>13 pt</option>
                                            <option value="14pt" <?= ($v['signer_name_size'] ?? '') === '14pt' ? 'selected' : '' ?>>14 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="signer_name" id="signer_name" value="<?= e($v['signer_name']) ?>" required>
                            </div>
                            <div class="builder-field">
                                <div class="field-header-flex">
                                    <label>NIP / NIDN Pejabat</label>
                                    <div class="size-pill-group">
                                        <span>Font:</span>
                                        <select name="signer_number_size" id="signer_number_size" class="size-select-sm">
                                            <option value="8.5pt" <?= ($v['signer_number_size'] ?? '') === '8.5pt' ? 'selected' : '' ?>>8.5 pt</option>
                                            <option value="9pt" <?= ($v['signer_number_size'] ?? '') === '9pt' ? 'selected' : '' ?>>9 pt</option>
                                            <option value="9.5pt" <?= ($v['signer_number_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                            <option value="10pt" <?= ($v['signer_number_size'] ?? '10pt') === '10pt' ? 'selected' : '' ?>>10 pt (Standar)</option>
                                            <option value="10.5pt" <?= ($v['signer_number_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                            <option value="11pt" <?= ($v['signer_number_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="signer_number" id="signer_number" value="<?= e($v['signer_number']) ?>">
                            </div>
                        </div>

                        <div class="builder-grid-2" style="margin-top:8px;">
                            <div class="builder-field">
                                <label>Tinggi Ruang Tanda Tangan</label>
                                <select name="signature_space_height" id="signature_space_height">
                                    <option value="45px" <?= ($v['signature_space_height'] ?? '') === '45px' ? 'selected' : '' ?>>Kecil / Ringkas (45 px)</option>
                                    <option value="65px" <?= ($v['signature_space_height'] ?? '') === '65px' ? 'selected' : '' ?>>Sedang (65 px)</option>
                                    <option value="80px" <?= ($v['signature_space_height'] ?? '80px') === '80px' ? 'selected' : '' ?>>Standar Resmi (80 px)</option>
                                    <option value="100px" <?= ($v['signature_space_height'] ?? '') === '100px' ? 'selected' : '' ?>>Tinggi (100 px)</option>
                                    <option value="120px" <?= ($v['signature_space_height'] ?? '') === '120px' ? 'selected' : '' ?>>Ekstra Tinggi (120 px)</option>
                                </select>
                            </div>

                            <div class="builder-field">
                                <label>Ukuran Gambar Tanda Tangan</label>
                                <select name="signature_size" id="signature_size">
                                    <option value="small" <?= ($v['signature_size'] ?? '') === 'small' ? 'selected' : '' ?>>Kecil (50 px)</option>
                                    <option value="medium" <?= ($v['signature_size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Sedang (70 px)</option>
                                    <option value="large" <?= ($v['signature_size'] ?? '') === 'large' ? 'selected' : '' ?>>Besar (90 px)</option>
                                    <option value="xlarge" <?= ($v['signature_size'] ?? '') === 'xlarge' ? 'selected' : '' ?>>Ekstra Besar (110 px)</option>
                                </select>
                            </div>
                        </div>

                        <div class="builder-field full" style="margin-top:10px;">
                            <label>Gambar Tanda Tangan</label>
                            <div class="builder-upload-box">
                                <div class="builder-upload-preview" id="signPreviewBox">
                                    <?php if (!empty($v['signature'])): ?>
                                        <img src="<?= e($v['signature']) ?>" alt="Signature">
                                    <?php else: ?>
                                        <span class="placeholder-icon"><i class="ri-quill-pen-line"></i></span>
                                    <?php endif; ?>
                                </div>
                                <div class="builder-upload-info">
                                    <strong>Unggah Tanda Tangan (PNG Transparan)</strong>
                                    <div class="builder-upload-actions">
                                        <input type="file" id="signFileInput" name="signature_file" accept="image/png,image/jpeg" style="display:none;">
                                        <button type="button" class="secondary-button" onclick="document.getElementById('signFileInput').click();"><i class="ri-upload-2-line"></i>Pilih Gambar</button>
                                        <button type="button" class="secondary-button" id="removeSignBtn" style="<?= empty($v['signature']) ? 'display:none;' : '' ?>color:var(--rose);"><i class="ri-delete-bin-line"></i>Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon rose"><i class="ri-copper-coin-line"></i></span>
                            <div>
                                <h3>Cap / Stempel Resmi Dinas</h3>
                                <p>Cap lembaga dengan perataan dan opasitas realistis.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Gambar Stempel (PNG Transparan)</label>
                            <div class="builder-upload-box">
                                <div class="builder-upload-preview" id="stampPreviewBox">
                                    <?php if (!empty($v['stamp'])): ?>
                                        <img src="<?= e($v['stamp']) ?>" alt="Stamp">
                                    <?php else: ?>
                                        <span class="placeholder-icon"><i class="ri-copper-coin-line"></i></span>
                                    <?php endif; ?>
                                </div>
                                <div class="builder-upload-info">
                                    <strong>Unggah Cap Stempel Resmi</strong>
                                    <div class="builder-upload-actions">
                                        <input type="file" id="stampFileInput" name="stamp_file" accept="image/png,image/jpeg" style="display:none;">
                                        <button type="button" class="secondary-button" onclick="document.getElementById('stampFileInput').click();"><i class="ri-upload-2-line"></i>Pilih Gambar</button>
                                        <button type="button" class="secondary-button" id="removeStampBtn" style="<?= empty($v['stamp']) ? 'display:none;' : '' ?>color:var(--rose);"><i class="ri-delete-bin-line"></i>Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="builder-grid-3">
                            <div class="builder-field">
                                <label>Posisi Stempel</label>
                                <select name="stamp_position" id="stamp_position">
                                    <option value="left" <?= ($v['stamp_position'] ?? 'left') === 'left' ? 'selected' : '' ?>>Kiri Menimpa TTD</option>
                                    <option value="center" <?= ($v['stamp_position'] ?? '') === 'center' ? 'selected' : '' ?>>Tengah TTD</option>
                                    <option value="right" <?= ($v['stamp_position'] ?? '') === 'right' ? 'selected' : '' ?>>Kanan TTD</option>
                                </select>
                            </div>
                            <div class="builder-field">
                                <label>Ukuran Diameter Cap</label>
                                <select name="stamp_size" id="stamp_size">
                                    <option value="small" <?= ($v['stamp_size'] ?? '') === 'small' ? 'selected' : '' ?>>Kecil (65 px)</option>
                                    <option value="medium" <?= ($v['stamp_size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Sedang (85 px)</option>
                                    <option value="large" <?= ($v['stamp_size'] ?? '') === 'large' ? 'selected' : '' ?>>Besar (105 px)</option>
                                    <option value="xlarge" <?= ($v['stamp_size'] ?? '') === 'xlarge' ? 'selected' : '' ?>>Ekstra Besar (125 px)</option>
                                </select>
                            </div>
                            <div class="builder-field">
                                <label>Opasitas Stempel</label>
                                <select name="stamp_opacity" id="stamp_opacity">
                                    <option value="100" <?= ($v['stamp_opacity'] ?? '85') === '100' ? 'selected' : '' ?>>100% (Pekat)</option>
                                    <option value="85" <?= ($v['stamp_opacity'] ?? '85') === '85' ? 'selected' : '' ?>>85% (Standar Tinta)</option>
                                    <option value="70" <?= ($v['stamp_opacity'] ?? '85') === '70' ? 'selected' : '' ?>>70% (Transparan)</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon teal"><i class="ri-file-copy-line"></i></span>
                            <div>
                                <h3>Tembusan & Catatan Kaki</h3>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Tembusan</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="copies_size" id="copies_size" class="size-select-sm">
                                        <option value="8.5pt" <?= ($v['copies_size'] ?? '') === '8.5pt' ? 'selected' : '' ?>>8.5 pt</option>
                                        <option value="9pt" <?= ($v['copies_size'] ?? '') === '9pt' ? 'selected' : '' ?>>9 pt</option>
                                        <option value="9.5pt" <?= ($v['copies_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                        <option value="10pt" <?= ($v['copies_size'] ?? '10pt') === '10pt' ? 'selected' : '' ?>>10 pt (Standar)</option>
                                        <option value="10.5pt" <?= ($v['copies_size'] ?? '') === '10.5pt' ? 'selected' : '' ?>>10.5 pt</option>
                                        <option value="11pt" <?= ($v['copies_size'] ?? '') === '11pt' ? 'selected' : '' ?>>11 pt</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="copies" id="copies" rows="3"><?= e($v['copies']) ?></textarea>
                        </div>
                        <div class="builder-field full">
                            <div class="field-header-flex">
                                <label>Catatan Kaki Dokumen</label>
                                <div class="size-pill-group">
                                    <span>Font:</span>
                                    <select name="footer_size" id="footer_size" class="size-select-sm">
                                        <option value="7.5pt" <?= ($v['footer_size'] ?? '') === '7.5pt' ? 'selected' : '' ?>>7.5 pt</option>
                                        <option value="8pt" <?= ($v['footer_size'] ?? '') === '8pt' ? 'selected' : '' ?>>8 pt</option>
                                        <option value="8.5pt" <?= ($v['footer_size'] ?? '8.5pt') === '8.5pt' ? 'selected' : '' ?>>8.5 pt (Standar)</option>
                                        <option value="9pt" <?= ($v['footer_size'] ?? '') === '9pt' ? 'selected' : '' ?>>9 pt</option>
                                        <option value="9.5pt" <?= ($v['footer_size'] ?? '') === '9.5pt' ? 'selected' : '' ?>>9.5 pt</option>
                                        <option value="10pt" <?= ($v['footer_size'] ?? '') === '10pt' ? 'selected' : '' ?>>10 pt</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="footer_note" id="footer_note" value="<?= e($v['footer_note']) ?>">
                        </div>
                    </section>
                </div>

                <!-- Action Bar -->
                <div class="builder-actions-bar">
                    <a class="secondary-button" href="?page=document-detail&id=<?= $id ?>"><i class="ri-arrow-left-line"></i>Batal</a>
                    <button class="primary-button" type="submit"><i class="ri-save-3-line"></i>Simpan Perubahan Format</button>
                </div>
            </div>

            <!-- KOLOM KANAN: Live Preview -->
            <div class="builder-preview-col" id="previewCol">
                <div class="preview-container-card">
                    <div class="preview-bar">
                        <div class="preview-bar-title"><i class="ri-eye-line" style="color:var(--primary);"></i><span>Pratinjau Lembar A4</span></div>
                        <div class="preview-bar-actions">
                            <button type="button" id="zoomOutBtn" title="Perkecil" onclick="window.applyA4Zoom(-10); return false;"><i class="ri-subtract-line"></i></button>
                            <b id="zoomLabel">100%</b>
                            <button type="button" id="zoomInBtn" title="Perbesar" onclick="window.applyA4Zoom(10); return false;"><i class="ri-add-line"></i></button>
                            <button type="button" id="zoomResetBtn" title="Reset Zoom" style="width:auto;padding:0 8px;font-size:11px;font-weight:700;" onclick="window.resetA4Zoom(); return false;">Reset</button>
                        </div>
                    </div>
                    <div class="a4-viewport" id="a4Viewport">
                        <div class="a4-sheet" id="a4Sheet">
                            <table class="a4-kop-table" id="a4KopTable">
                                <tr>
                                    <td class="a4-kop-logo-left" id="a4LogoLeftCell" style="<?= empty($v['logo']) ? 'display:none;' : '' ?>">
                                        <img src="<?= e($v['logo'] ?? '') ?>" class="a4-kop-logo-img" id="a4LogoLeftImg" alt="Logo">
                                    </td>
                                    <td class="a4-kop-text" id="a4KopTextCell" style="text-align:<?= ($v['kop_align'] ?? 'center') === 'left' ? 'left' : 'center' ?>;">
                                        <div class="a4-kop-inst-1" id="a4KopInst1"><?= e($v['institution']) ?></div>
                                        <div class="a4-kop-inst-2" id="a4KopInst2"><?= e($v['unit']) ?></div>
                                        <div class="a4-kop-address" id="a4KopAddress"><?= e($v['address']) ?></div>
                                        <div class="a4-kop-contact" id="a4KopContact"><?= e($v['contact']) ?></div>
                                    </td>
                                </tr>
                            </table>

                            <div class="a4-line-double" id="a4KopLine"></div>

                            <table class="a4-meta-table">
                                <tr>
                                    <td width="60%">
                                        <table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;">
                                            <tr><td class="a4-meta-label">Nomor</td><td class="a4-meta-colon">:</td><td><span id="a4Number"><?= e($v['custom_number'] ?: $d['number']) ?></span></td></tr>
                                            <tr><td class="a4-meta-label">Sifat</td><td class="a4-meta-colon">:</td><td><span id="a4Confidentiality"><?= e($v['confidentiality'] ?? 'Biasa') ?></span></td></tr>
                                            <tr><td class="a4-meta-label">Lampiran</td><td class="a4-meta-colon">:</td><td><span id="a4Attachment"><?= e($v['attachment'] ?? '-') ?></span></td></tr>
                                            <tr><td class="a4-meta-label">Perihal</td><td class="a4-meta-colon">:</td><td><strong id="a4Title"><?= e($v['title'] ?: $d['title']) ?></strong></td></tr>
                                        </table>
                                    </td>
                                    <td width="40%" align="right" valign="top">
                                        <div id="a4DateCity"><?= e($v['city'] ? $v['city'] . ', ' : '') . e($v['date']) ?></div>
                                        <div style="text-align:left;display:inline-block;margin-top:14px;">
                                            <div>Kepada Yth.</div>
                                            <div id="a4Recipient" style="white-space:pre-line;"><?= e($v['recipient']) ?></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="a4-opening" id="a4GreetingOpening"><?= e($v['greeting_opening']) ?></div>
                            <div class="a4-body" id="a4Opening"><?= e($v['opening']) ?></div>
                            <div class="a4-body" id="a4Body"><?= e($v['body']) ?></div>
                            <div class="a4-closing" id="a4Closing"><?= e($v['closing']) ?></div>
                            <div class="a4-opening" id="a4GreetingClosing" style="margin-bottom:12px;"><?= e($v['greeting_closing']) ?></div>

                            <div class="a4-sign-wrap">
                                <div class="a4-sign-box">
                                    <div class="a4-sign-position" id="a4SignPosition"><?= e($v['signer_position']) ?></div>
                                    <div class="a4-sign-middle" id="a4SignMiddle">
                                        <img src="<?= e($v['signature'] ?? '') ?>" class="a4-signature-img" id="a4SignImg" style="<?= empty($v['signature']) ? 'display:none;' : '' ?>" alt="TTD">
                                        <img src="<?= e($v['stamp'] ?? '') ?>" class="a4-stamp-img a4-stamp-<?= e($v['stamp_position'] ?? 'left') ?>" id="a4StampImg" style="<?= empty($v['stamp']) ? 'display:none;' : '' ?>;opacity:<?= ((int)($v['stamp_opacity'] ?? 85))/100 ?>;" alt="Cap">
                                    </div>
                                    <div class="a4-sign-name" id="a4SignName"><?= e($v['signer_name']) ?></div>
                                    <div class="a4-sign-nip" id="a4SignNip"><?= e($v['signer_number']) ?></div>
                                </div>
                            </div>

                            <div class="a4-copies" id="a4CopiesWrap" style="<?= empty($v['copies']) ? 'display:none;' : '' ?>">
                                <strong><u>Tembusan:</u></strong>
                                <div id="a4Copies" style="white-space:pre-line;margin-top:2px;"><?= e($v['copies']) ?></div>
                            </div>

                            <div class="a4-footer-note" id="a4FooterNoteWrap" style="<?= empty($v['footer_note']) ? 'display:none;' : '' ?>">
                                <span id="a4FooterNote"><?= e($v['footer_note']) ?></span>
                                <span>Halaman 1/1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
