<?php RbacService::authorize('agendas.view'); ?>
<link rel="stylesheet" href="assets/css/agenda.css?v=<?= filemtime(__DIR__.'/../../../assets/css/agenda.css') ?>">
<div class="page-shell" id="agendaApp">
<header class="page-header"><div><p class="eyebrow">PENUGASAN</p><h1>Agenda<span>.</span></h1><p>Klik tanggal untuk menambah kegiatan, atau klik agenda untuk melihat rincian. Waktu dalam WIB.</p></div><button class="primary-button" id="agendaAdd">Tambah Agenda</button></header>
<div id="agendaMessage" role="status" aria-live="polite"></div>
<section class="panel calendar-panel"><div class="calendar-toolbar">
<button class="icon-button" id="agendaPrev" aria-label="Bulan sebelumnya">‹</button><h2 id="agendaMonth" aria-live="polite"></h2><button class="icon-button" id="agendaNext" aria-label="Bulan berikutnya">›</button><button class="secondary-button" id="agendaToday">Hari ini</button>
</div><div class="calendar-days"><?php foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day): ?><span><?= $day ?></span><?php endforeach; ?></div><div class="calendar-cells" id="agendaCells"></div></section>
<section class="panel agenda-month-list"><div class="panel-head"><h2>Kegiatan bulan ini</h2><button class="secondary-button" id="agendaReload">Segarkan</button></div><div id="agendaList"></div></section>
</div>
<dialog id="agendaDialog" aria-labelledby="agendaDialogTitle"><header><h2 id="agendaDialogTitle">Agenda</h2><button class="secondary-button" id="agendaClose" type="button">Tutup</button></header>
<form id="agendaEdit"><input name="id" type="hidden"><p id="agendaSource"></p><div id="agendaError" role="alert"></div><div class="form-grid">
<label class="full"><span>Judul *</span><input name="title" required maxlength="255"></label>
<label><span>Mulai (WIB) *</span><input name="starts_at" type="datetime-local" required></label><label><span>Selesai (WIB) *</span><input name="ends_at" type="datetime-local" required></label>
<label class="full"><span>Lokasi</span><input name="location" maxlength="255"></label>
<label class="full"><span>Deskripsi</span><textarea name="description" rows="4" maxlength="10000"></textarea></label>
<label><span>Status</span><select name="status"><option value="scheduled">Terjadwal</option><option value="completed">Selesai</option><option value="cancelled">Dibatalkan</option></select></label></div>
<div class="preview-actions"><button class="primary-button" id="agendaSave" type="submit">Simpan agenda</button><button class="secondary-button" id="agendaCancel" type="button">Batalkan agenda</button><a class="secondary-button" id="agendaDocument" hidden>Buka surat asal</a></div></form></dialog>
<script src="assets/js/agenda.js" defer></script>
