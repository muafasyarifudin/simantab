<?php
$current = $_GET['page'] ?? 'dashboard';
$totalDocuments = isset($documents) ? count($documents) : 0;
$totalApprovals = isset($approvals) ? count($approvals) : 0;
$totalAssignments = isset($assignments) ? count($assignments) : 0;
$totalPendingReports = isset($assignments) ? count(array_filter($assignments, fn($a) => ($a['status'] ?? '') !== 'Selesai')) : 0;
?>
<aside class="app-sidebar" id="appSidebar">
 <a class="brand" href="?page=dashboard"><span class="brand-mark"><i class="ri-mail-send-line"></i></span><span><strong>SIMANTAP<span>.</span></strong><small>ADMINISTRASI TERPADU</small></span></a>
 <nav class="sidebar-nav" aria-label="Navigasi utama">
   <a class="nav-link <?= active_route('dashboard') ?>" href="?page=dashboard" title="Dashboard"><span class="nav-icon blue"><i class="ri-dashboard-3-line"></i></span><span>Dashboard</span></a>
   <p class="nav-title">MANAJEMEN SURAT</p>
   <a class="nav-link <?= active_route(['documents','document-detail']) ?>" href="?page=documents" title="Semua Surat"><span class="nav-icon teal"><i class="ri-file-list-3-line"></i></span><span>Semua Surat</span><span class="nav-count"><?= $totalDocuments ?></span></a>
   <a class="nav-link <?= active_route('create-document') ?>" href="?page=create-document" title="Buat Surat"><span class="nav-icon blue"><i class="ri-file-add-line"></i></span><span>Buat Surat</span></a>
   <a class="nav-link <?= active_route('approvals') ?>" href="?page=approvals" title="Persetujuan"><span class="nav-icon amber"><i class="ri-file-check-line"></i></span><span>Persetujuan</span><?php if ($totalApprovals > 0): ?><span class="nav-count danger"><?= $totalApprovals ?></span><?php endif; ?></a>
   <a class="nav-link <?= active_route('archive') ?>" href="?page=archive" title="Arsip Digital"><span class="nav-icon violet"><i class="ri-archive-line"></i></span><span>Arsip Digital</span></a>
   <p class="nav-title">PENUGASAN</p>
   <a class="nav-link <?= active_route('assignments') ?>" href="?page=assignments" title="Penugasan"><span class="nav-icon rose"><i class="ri-briefcase-4-line"></i></span><span>Penugasan</span><?php if ($totalAssignments > 0): ?><span class="nav-count"><?= $totalAssignments ?></span><?php endif; ?></a>
   <a class="nav-link <?= active_route('agenda') ?>" href="?page=agenda" title="Agenda"><span class="nav-icon violet"><i class="ri-calendar-event-line"></i></span><span>Agenda</span></a>
   <a class="nav-link <?= active_route('reports') ?>" href="?page=reports" title="Laporan"><span class="nav-icon teal"><i class="ri-survey-line"></i></span><span>Laporan</span><?php if ($totalPendingReports > 0): ?><span class="nav-count"><?= $totalPendingReports ?></span><?php endif; ?></a>
   <p class="nav-title">ADMINISTRASI</p>
   <a class="nav-link <?= active_route(['users','units']) ?>" href="?page=users" title="Pengguna & Unit"><span class="nav-icon cyan"><i class="ri-team-line"></i></span><span>Pengguna & Unit</span></a>
   <a class="nav-link <?= active_route(['templates','numbering']) ?>" href="?page=templates" title="Template & Nomor"><span class="nav-icon indigo"><i class="ri-layout-4-line"></i></span><span>Template & Nomor</span></a>
   <a class="nav-link <?= active_route('audit') ?>" href="?page=audit" title="Audit Log"><span class="nav-icon emerald"><i class="ri-shield-keyhole-line"></i></span><span>Audit Log</span></a>
   <a class="nav-link <?= active_route('changelog') ?>" href="?page=changelog" title="Changelog & Riwayat Versi"><span class="nav-icon blue"><i class="ri-git-commit-line"></i></span><span>Changelog</span><span class="badge emerald" style="font-size:0.7rem; font-weight:700; padding:0.15rem 0.4rem; border-radius:999px;">v<?= APP_VERSION ?></span></a>
  </nav>
 <div class="sidebar-help"><span class="help-icon"><i class="ri-customer-service-2-line"></i></span><div><strong>Butuh bantuan?</strong><small>Pusat bantuan LPSI</small></div><i class="ri-arrow-right-s-line"></i></div>
</aside>
