<?php
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function active_route(string|array $routes): string {
    $current = $_GET['page'] ?? 'dashboard';
    return in_array($current, (array)$routes, true) ? 'active' : '';
}
function status_class(string $status): string {
    return match ($status) {
        'Disetujui', 'Selesai', 'Didistribusikan', 'Ditandatangani' => 'teal',
        'Perlu Revisi', 'Ditolak', 'Terlambat' => 'rose',
        'Menunggu Persetujuan', 'Dalam Pemeriksaan', 'Menunggu Laporan' => 'amber',
        'Dalam Pelaksanaan' => 'violet',
        default => 'blue',
    };
}
function format_date_id(string $date): string {
    $months = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}
