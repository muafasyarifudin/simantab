<?php
$notifsList = $notifications ?? [];
$unreadNotifsCount = count(array_filter($notifsList, fn($n) => !empty($n['unread'])));
?>
<!-- Backdrop Overlay -->
<div class="drawer-backdrop" id="notifDrawerBackdrop" onclick="window.toggleNotificationDrawer(false);" aria-hidden="true"></div>

<!-- Off-Canvas Notification Drawer (Slide-out from Right) -->
<aside class="notification-drawer" id="notifDrawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle" aria-hidden="true">
    <!-- Header -->
    <div class="drawer-header">
        <div class="drawer-header-left">
            <span class="drawer-icon-wrap"><i class="ri-notification-3-line"></i></span>
            <div>
                <h3 id="drawerTitle">Notifikasi</h3>
                <span class="drawer-unread-badge" id="drawerUnreadBadge" style="<?= $unreadNotifsCount === 0 ? 'display:none;' : '' ?>">
                    <?= $unreadNotifsCount ?> belum dibaca
                </span>
            </div>
        </div>
        <div class="drawer-header-actions">
            <button type="button" class="drawer-action-btn" id="drawerMarkAllBtn" title="Tandai semua dibaca" onclick="window.markAllNotificationsRead();">
                <i class="ri-check-double-line"></i>
                <span>Tandai dibaca</span>
            </button>
            <button type="button" class="drawer-close-btn" aria-label="Tutup" onclick="window.toggleNotificationDrawer(false);">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="drawer-tabs">
        <button type="button" class="drawer-tab active" data-filter="all" onclick="window.filterDrawerNotifs('all', this);">
            Semua <span class="tab-badge"><?= count($notifsList) ?></span>
        </button>
        <button type="button" class="drawer-tab" data-filter="unread" onclick="window.filterDrawerNotifs('unread', this);">
            Belum Dibaca <span class="tab-badge" id="tabUnreadCount"><?= $unreadNotifsCount ?></span>
        </button>
    </div>

    <!-- Notifications List -->
    <div class="drawer-body" id="drawerNotifList">
        <?php if (empty($notifsList)): ?>
            <div class="drawer-empty-state">
                <span class="empty-icon"><i class="ri-notification-off-line"></i></span>
                <h4>Tidak Ada Notifikasi</h4>
                <p>Semua pembaruan dokumen dan penugasan akan muncul di sini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifsList as $n): ?>
                <a href="<?= e($n['deep_link'] ?? '?page=approvals') ?>" 
                   class="drawer-notif-item <?= !empty($n['unread']) ? 'unread' : '' ?>" 
                   data-unread="<?= !empty($n['unread']) ? '1' : '0' ?>"
                   onclick="window.toggleNotificationDrawer(false);">
                    <div class="notif-item-icon <?= e($n['tone'] ?? 'blue') ?>">
                        <i class="<?= e($n['icon'] ?? 'ri-notification-3-line') ?>"></i>
                    </div>
                    <div class="notif-item-content">
                        <div class="notif-item-title-row">
                            <strong><?= e($n['title']) ?></strong>
                            <?php if (!empty($n['unread'])): ?>
                                <span class="notif-unread-dot"></span>
                            <?php endif; ?>
                        </div>
                        <p><?= e($n['text']) ?></p>
                        <time><i class="ri-time-line"></i> <?= e($n['time']) ?></time>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="drawer-footer">
        <a href="?page=notifications" class="drawer-footer-link" onclick="window.toggleNotificationDrawer(false);">
            <span>Buka Pusat Notifikasi Lengkap</span>
            <i class="ri-arrow-right-line"></i>
        </a>
    </div>
</aside>
