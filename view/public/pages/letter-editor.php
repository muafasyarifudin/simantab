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
        $v['kop_align'] = $_POST['kop_align'] ?? 'center';
        $v['kop_line_style'] = $_POST['kop_line_style'] ?? 'double';
        $v['logo_align'] = $_POST['logo_align'] ?? 'left';
        $v['logo_size'] = $_POST['logo_size'] ?? 'medium';
        $v['font_family'] = $_POST['font_family'] ?? 'serif';
        $v['custom_number'] = trim((string)($_POST['custom_number'] ?? ''));
        $v['confidentiality'] = $_POST['confidentiality'] ?? 'Biasa';
        $v['attachment'] = trim((string)($_POST['attachment'] ?? '-'));
        $v['title'] = trim((string)($_POST['title'] ?? $d['title']));
        $v['recipient'] = trim((string)($_POST['recipient'] ?? ''));
        $v['greeting_opening'] = trim((string)($_POST['greeting_opening'] ?? 'Dengan hormat,'));
        $v['opening'] = trim((string)($_POST['opening'] ?? ''));
        $v['body'] = trim((string)($_POST['body'] ?? ''));
        $v['closing'] = trim((string)($_POST['closing'] ?? ''));
        $v['greeting_closing'] = trim((string)($_POST['greeting_closing'] ?? 'Hormat kami,'));
        $v['city'] = trim((string)($_POST['city'] ?? ''));
        $v['date'] = $_POST['document_date'] ?? date('Y-m-d');
        $v['signer_position'] = trim((string)($_POST['signer_position'] ?? ''));
        $v['signer_name'] = trim((string)($_POST['signer_name'] ?? ''));
        $v['signer_number'] = trim((string)($_POST['signer_number'] ?? ''));
        $v['stamp_position'] = $_POST['stamp_position'] ?? 'left';
        $v['stamp_opacity'] = $_POST['stamp_opacity'] ?? '85';
        $v['copies'] = trim((string)($_POST['copies'] ?? ''));
        $v['footer_note'] = trim((string)($_POST['footer_note'] ?? ''));

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
        <div style="display:flex;gap:10px;align-items:center;">
            <div class="builder-view-toggle">
                <button type="button" class="active" data-view-mode="split"><i class="ri-layout-column-line"></i>Split</button>
                <button type="button" data-view-mode="form"><i class="ri-edit-box-line"></i>Form</button>
                <button type="button" data-view-mode="preview"><i class="ri-file-paper-line"></i>A4</button>
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
                <div class="builder-tab-pane active" id="tab-meta">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-file-settings-line"></i></span>
                            <div>
                                <h3>Identitas & Nomor Dokumen</h3>
                                <p>Perbarui perihal, nomor resmi, dan sifat dokumen.</p>
                            </div>
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field full">
                                <label>Perihal / Hal Surat <b>*</b></label>
                                <input type="text" name="title" id="title" required maxlength="500" value="<?= e($v['title'] ?: $d['title']) ?>">
                            </div>

                            <div class="builder-field">
                                <label>Judul Kategori / Heading Kertas</label>
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
                                <label>Lampiran</label>
                                <input type="text" name="attachment" id="attachment" value="<?= e($v['attachment'] ?? '-') ?>">
                            </div>

                            <div class="builder-field">
                                <label>Tanggal Surat</label>
                                <input type="date" name="document_date" id="document_date" value="<?= e($v['date'] ?? date('Y-m-d')) ?>">
                            </div>
                        </div>
                    </section>
                </div>

                <!-- TAB 2: KOP, LOGO & GARIS PEMBATAS -->
                <div class="builder-tab-pane" id="tab-kop">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon blue"><i class="ri-layout-top-line"></i></span>
                            <div>
                                <h3>Kepala Surat (KOP) & Tipografi</h3>
                                <p>Sesuaikan nama instansi, alamat, dan font surat.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Nama Lembaga Induk / Instansi Atas (Baris 1) <b>*</b></label>
                            <input type="text" name="institution" id="kop_inst1" value="<?= e($v['institution']) ?>" required>
                        </div>

                        <div class="builder-field full">
                            <label>Nama Satuan Kerja / Unit / Fakultas (Baris 2)</label>
                            <input type="text" name="unit" id="kop_inst2" value="<?= e($v['unit']) ?>">
                        </div>

                        <div class="builder-field full">
                            <label>Alamat Lengkap KOP <b>*</b></label>
                            <textarea name="address" id="kop_address" rows="2" required><?= e($v['address']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Kontak KOP (Telepon, Email, Website)</label>
                            <input type="text" name="contact" id="kop_contact" value="<?= e($v['contact']) ?>">
                        </div>

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Perataan KOP</label>
                                <div class="pill-selector">
                                    <label class="pill-opt <?= ($v['kop_align'] ?? 'center') === 'center' ? 'selected' : '' ?>">
                                        <input type="radio" name="kop_align" value="center" <?= ($v['kop_align'] ?? 'center') === 'center' ? 'checked' : '' ?>>
                                        <span>Tengah</span>
                                    </label>
                                    <label class="pill-opt <?= ($v['kop_align'] ?? '') === 'left' ? 'selected' : '' ?>">
                                        <input type="radio" name="kop_align" value="left" <?= ($v['kop_align'] ?? '') === 'left' ? 'checked' : '' ?>>
                                        <span>Kiri</span>
                                    </label>
                                </div>
                            </div>

                            <div class="builder-field">
                                <label>Gaya Huruf</label>
                                <div class="pill-selector">
                                    <label class="pill-opt <?= ($v['font_family'] ?? 'serif') === 'serif' ? 'selected' : '' ?>">
                                        <input type="radio" name="font_family" value="serif" <?= ($v['font_family'] ?? 'serif') === 'serif' ? 'checked' : '' ?>>
                                        <span>Serif (Times)</span>
                                    </label>
                                    <label class="pill-opt <?= ($v['font_family'] ?? '') === 'sans' ? 'selected' : '' ?>">
                                        <input type="radio" name="font_family" value="sans" <?= ($v['font_family'] ?? '') === 'sans' ? 'checked' : '' ?>>
                                        <span>Sans-Serif</span>
                                    </label>
                                </div>
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
                                <label>Ukuran Logo</label>
                                <select name="logo_size" id="logo_size">
                                    <option value="small" <?= ($v['logo_size'] ?? '') === 'small' ? 'selected' : '' ?>>Kecil (56 px)</option>
                                    <option value="medium" <?= ($v['logo_size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Sedang (72 px)</option>
                                    <option value="large" <?= ($v['logo_size'] ?? '') === 'large' ? 'selected' : '' ?>>Besar (88 px)</option>
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
                <div class="builder-tab-pane" id="tab-content">
                    <section class="builder-card">
                        <div class="builder-card-title">
                            <span class="section-icon amber"><i class="ri-mail-line"></i></span>
                            <div>
                                <h3>Penerima & Pembukaan</h3>
                                <p>Tujuan naskah dinas dan salam pembuka.</p>
                            </div>
                        </div>

                        <div class="builder-field full">
                            <label>Tujuan / Penerima Surat (Kepada Yth.) <b>*</b></label>
                            <textarea name="recipient" id="recipient" rows="3" required><?= e($v['recipient']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Salam Pembuka</label>
                            <input type="text" name="greeting_opening" id="greeting_opening" value="<?= e($v['greeting_opening']) ?>">
                        </div>

                        <div class="builder-field full">
                            <label>Paragraf Pembuka / Dasar Surat</label>
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

                        <div class="builder-field full">
                            <label>Isi Surat Lengkap <b>*</b></label>
                            <textarea name="body" id="body" rows="8" required><?= e($v['body']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Paragraf Penutup</label>
                            <textarea name="closing" id="closing" rows="2"><?= e($v['closing']) ?></textarea>
                        </div>

                        <div class="builder-field full">
                            <label>Salam Penutup</label>
                            <input type="text" name="greeting_closing" id="greeting_closing" value="<?= e($v['greeting_closing']) ?>">
                        </div>
                    </section>
                </div>

                <!-- TAB 4: TANDA TANGAN & CAP -->
                <div class="builder-tab-pane" id="tab-sign">
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
                                <label>Jabatan Penandatangan <b>*</b></label>
                                <input type="text" name="signer_position" id="signer_position" value="<?= e($v['signer_position']) ?>" required>
                            </div>
                            <div class="builder-field">
                                <label>Nama Lengkap Pejabat <b>*</b></label>
                                <input type="text" name="signer_name" id="signer_name" value="<?= e($v['signer_name']) ?>" required>
                            </div>
                            <div class="builder-field">
                                <label>NIP / NIDN Pejabat</label>
                                <input type="text" name="signer_number" id="signer_number" value="<?= e($v['signer_number']) ?>">
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

                        <div class="builder-grid-2">
                            <div class="builder-field">
                                <label>Posisi Stempel</label>
                                <select name="stamp_position" id="stamp_position">
                                    <option value="left" <?= ($v['stamp_position'] ?? 'left') === 'left' ? 'selected' : '' ?>>Kiri Menimpa TTD</option>
                                    <option value="center" <?= ($v['stamp_position'] ?? '') === 'center' ? 'selected' : '' ?>>Tengah TTD</option>
                                    <option value="right" <?= ($v['stamp_position'] ?? '') === 'right' ? 'selected' : '' ?>>Kanan TTD</option>
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
                            <label>Tembusan</label>
                            <textarea name="copies" id="copies" rows="3"><?= e($v['copies']) ?></textarea>
                        </div>
                        <div class="builder-field full">
                            <label>Catatan Kaki Dokumen</label>
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
                            <button type="button" id="zoomOutBtn"><i class="ri-subtract-line"></i></button>
                            <b id="zoomLabel">100%</b>
                            <button type="button" id="zoomInBtn"><i class="ri-add-line"></i></button>
                            <button type="button" id="zoomResetBtn" style="width:auto;padding:0 8px;font-size:11px;font-weight:700;">Reset</button>
                        </div>
                    </div>
                    <div class="a4-viewport">
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

<script>
window.switchBuilderTab = function(idx) {
    const paneIds = ['tab-meta', 'tab-kop', 'tab-content', 'tab-sign'];
    const btns = document.querySelectorAll('.builder-tab-btn');
    const panes = document.querySelectorAll('.builder-tab-pane');

    btns.forEach((b, i) => {
        if (i === idx) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });

    paneIds.forEach((pid, i) => {
        const pane = document.getElementById(pid);
        if (pane) {
            if (i === idx) {
                pane.classList.add('active');
                pane.style.display = 'block';
            } else {
                pane.classList.remove('active');
                pane.style.display = 'none';
            }
        }
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
};

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.builder-tab-btn');
    if (!btn) return;
    const tabNav = btn.closest('.builder-tabs-nav');
    if (!tabNav) return;
    const allBtns = Array.from(tabNav.querySelectorAll('.builder-tab-btn'));
    const idx = allBtns.indexOf(btn);
    if (idx !== -1) {
        window.switchBuilderTab(idx);
    }
});

    const workbench = document.getElementById('builderWorkbench');
    document.querySelectorAll('[data-view-mode]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-view-mode]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            workbench.classList.remove('view-form-only', 'view-preview-only');
            if (btn.dataset.viewMode === 'form') workbench.classList.add('view-form-only');
            if (btn.dataset.viewMode === 'preview') workbench.classList.add('view-preview-only');
        });
    });

    let currentZoom = 100;
    const a4Sheet = document.getElementById('a4Sheet');
    const zoomLabel = document.getElementById('zoomLabel');
    function applyZoom(z) {
        currentZoom = Math.min(140, Math.max(60, z));
        a4Sheet.style.transform = `scale(${currentZoom / 100})`;
        zoomLabel.textContent = `${currentZoom}%`;
    }
    document.getElementById('zoomInBtn')?.addEventListener('click', () => applyZoom(currentZoom + 10));
    document.getElementById('zoomOutBtn')?.addEventListener('click', () => applyZoom(currentZoom - 10));
    document.getElementById('zoomResetBtn')?.addEventListener('click', () => applyZoom(100));

    function updateA4() {
        document.getElementById('a4KopInst1').textContent = document.getElementById('kop_inst1').value;
        document.getElementById('a4KopInst2').textContent = document.getElementById('kop_inst2').value;
        document.getElementById('a4KopAddress').textContent = document.getElementById('kop_address').value;
        document.getElementById('a4KopContact').textContent = document.getElementById('kop_contact').value;

        const kopAlign = document.querySelector('input[name="kop_align"]:checked')?.value || 'center';
        document.getElementById('a4KopTextCell').style.textAlign = kopAlign;

        const fontFamily = document.querySelector('input[name="font_family"]:checked')?.value || 'serif';
        a4Sheet.style.fontFamily = fontFamily === 'sans' ? '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif' : '"Times New Roman", Times, Georgia, serif';

        const lineStyle = document.querySelector('input[name="kop_line_style"]:checked')?.value || 'double';
        const lineEl = document.getElementById('a4KopLine');
        lineEl.className = lineStyle === 'single_thick' ? 'a4-line-single-thick' : (lineStyle === 'single_thin' ? 'a4-line-single-thin' : (lineStyle === 'dashed' ? 'a4-line-dashed' : (lineStyle === 'none' ? 'a4-line-none' : 'a4-line-double')));

        document.getElementById('a4Number').textContent = document.getElementById('custom_number').value || '[Nomor Surat]';
        document.getElementById('a4Confidentiality').textContent = document.getElementById('confidentiality').value;
        document.getElementById('a4Attachment').textContent = document.getElementById('attachment').value || '-';
        document.getElementById('a4Title').textContent = document.getElementById('title').value;

        const city = document.getElementById('city').value.trim();
        const docDate = document.getElementById('document_date').value;
        document.getElementById('a4DateCity').textContent = city ? `${city}, ${docDate}` : docDate;

        document.getElementById('a4Recipient').textContent = document.getElementById('recipient').value;
        document.getElementById('a4GreetingOpening').textContent = document.getElementById('greeting_opening').value;
        document.getElementById('a4Opening').textContent = document.getElementById('opening').value;
        document.getElementById('a4Body').textContent = document.getElementById('body').value;
        document.getElementById('a4Closing').textContent = document.getElementById('closing').value;
        document.getElementById('a4GreetingClosing').textContent = document.getElementById('greeting_closing').value;

        document.getElementById('a4SignPosition').textContent = document.getElementById('signer_position').value;
        document.getElementById('a4SignName').textContent = document.getElementById('signer_name').value;
        document.getElementById('a4SignNip').textContent = document.getElementById('signer_number').value;

        const stampPos = document.getElementById('stamp_position').value;
        const stampEl = document.getElementById('a4StampImg');
        stampEl.className = 'a4-stamp-img a4-stamp-' + stampPos;
        stampEl.style.opacity = (parseInt(document.getElementById('stamp_opacity').value || '85') / 100);

        const copies = document.getElementById('copies').value.trim();
        document.getElementById('a4CopiesWrap').style.display = copies ? 'block' : 'none';
        document.getElementById('a4Copies').textContent = copies;

        const footer = document.getElementById('footer_note').value.trim();
        document.getElementById('a4FooterNoteWrap').style.display = footer ? 'flex' : 'none';
        document.getElementById('a4FooterNote').textContent = footer;
    }

    const formInputs = document.querySelectorAll('#letterBuilderForm input:not([type="file"]), #letterBuilderForm select, #letterBuilderForm textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', updateA4);
        input.addEventListener('change', updateA4);
    });

    document.querySelectorAll('.pill-opt input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const name = radio.name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                r.closest('.pill-opt')?.classList.toggle('selected', r.checked);
            });
            updateA4();
        });
    });

    function handleImg(fileInput, base64Input, previewBox, a4Img, removeBtn) {
        fileInput?.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const b64 = e.target.result;
                base64Input.value = b64;
                previewBox.innerHTML = `<img src="${b64}">`;
                a4Img.src = b64;
                a4Img.style.display = 'inline-block';
                if (removeBtn) removeBtn.style.display = 'inline-flex';
                if (a4Img.id === 'a4LogoLeftImg') document.getElementById('a4LogoLeftCell').style.display = 'table-cell';
                updateA4();
            };
            reader.readAsDataURL(file);
        });

        removeBtn?.addEventListener('click', () => {
            fileInput.value = '';
            base64Input.value = '';
            previewBox.innerHTML = `<span class="placeholder-icon"><i class="ri-image-add-line"></i></span>`;
            a4Img.src = '';
            a4Img.style.display = 'none';
            removeBtn.style.display = 'none';
            if (a4Img.id === 'a4LogoLeftImg') document.getElementById('a4LogoLeftCell').style.display = 'none';
            updateA4();
        });
    }

    handleImg(document.getElementById('logoFileInput'), document.getElementById('logo_base64'), document.getElementById('logoPreviewBox'), document.getElementById('a4LogoLeftImg'), document.getElementById('removeLogoBtn'));
    handleImg(document.getElementById('signFileInput'), document.getElementById('signature_base64'), document.getElementById('signPreviewBox'), document.getElementById('a4SignImg'), document.getElementById('removeSignBtn'));
    handleImg(document.getElementById('stampFileInput'), document.getElementById('stamp_base64'), document.getElementById('stampPreviewBox'), document.getElementById('a4StampImg'), document.getElementById('removeStampBtn'));

    updateA4();
});
</script>
