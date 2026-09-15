<?php 
$user = $_SESSION['simantap_user']; 
$unreadNotificationsCount = count(array_filter($notifications ?? [], fn($n) => !empty($n['unread'])));
?>
<header class="topbar">
 <button type="button" class="icon-button" id="sidebarToggleBtn" aria-label="Toggle Sidebar Menu" title="Sembunyikan / Tampilkan Menu Sidebar" onclick="window.toggleMainSidebar(); return false;"><i class="ri-menu-2-line" id="sidebarToggleIcon"></i></button>
 <div class="top-search"><i class="ri-search-line"></i><input id="globalSearch" type="search" placeholder="Cari nomor, perihal, pegawai..." aria-label="Pencarian global"><kbd>Ctrl K</kbd></div>
 <div class="top-actions">
  <button type="button" class="icon-button" id="themeToggle" aria-label="Ubah tema"><i class="ri-moon-line"></i></button>
  <button type="button" class="icon-button notification-button" id="notificationDrawerBtn" aria-label="Notifikasi" onclick="window.toggleNotificationDrawer(true); return false;">
   <i class="ri-notification-3-line"></i>
   <span id="topbarNotifDot" style="<?= $unreadNotificationsCount === 0 ? 'display:none;' : '' ?>"></span>
  </button>
  <div class="dropdown"><button class="profile" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun"><span class="avatar"><?= e(strtoupper(substr($user['name'],0,2))) ?></span><span class="profile-copy"><strong><?= e($user['name']) ?></strong><small><?= e($user['role']) ?></small></span><i class="ri-arrow-down-s-line"></i></button><div class="dropdown-menu dropdown-menu-end"><span class="dropdown-item-text"><?= e($user['name']) ?></span><a class="dropdown-item" href="javascript:void(0)" onclick="window.toggleNotificationDrawer(true);">Notifikasi saya</a><form method="post" action="?page=logout"><input type="hidden" name="_token" value="<?= e(Security::csrfToken()) ?>"><button class="dropdown-item" type="submit">Keluar</button></form></div></div>
 </div>
</header>
