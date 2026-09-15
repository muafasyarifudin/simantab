<?php
$documents = [
 ['id'=>1,'number'=>'045/ST/LPSI/UMPO/IX/2026','title'=>'Penugasan Tim Audit Infrastruktur TI','type'=>'Surat Tugas','unit'=>'LPSI','date'=>'2026-09-15','status'=>'Menunggu Persetujuan','priority'=>'Segera','assignee'=>'Rektor'],
 ['id'=>2,'number'=>'044/SK/UMPO/IX/2026','title'=>'Penetapan Panitia Dies Natalis ke-40','type'=>'Surat Keputusan','unit'=>'Biro Umum','date'=>'2026-09-14','status'=>'Dalam Pemeriksaan','priority'=>'Penting','assignee'=>'Tata Usaha'],
 ['id'=>3,'number'=>'043/LAP/LPPM/IX/2026','title'=>'Laporan Capaian Penelitian Semester Gasal','type'=>'Surat Laporan','unit'=>'LPPM','date'=>'2026-09-13','status'=>'Perlu Revisi','priority'=>'Biasa','assignee'=>'Budi Santoso'],
 ['id'=>4,'number'=>'042/ST/BAAK/IX/2026','title'=>'Pendampingan Akreditasi Program Studi','type'=>'Surat Tugas','unit'=>'BAAK','date'=>'2026-09-12','status'=>'Didistribusikan','priority'=>'Penting','assignee'=>'8 pegawai'],
 ['id'=>5,'number'=>'DRAF-2026-091','title'=>'Undangan Rapat Koordinasi Pimpinan','type'=>'Surat Undangan','unit'=>'Rektorat','date'=>'2026-09-11','status'=>'Draft','priority'=>'Biasa','assignee'=>'-'],
 ['id'=>6,'number'=>'041/ST/LPSI/UMPO/IX/2026','title'=>'Pemutakhiran Data Sistem Akademik','type'=>'Surat Tugas','unit'=>'LPSI','date'=>'2026-09-10','status'=>'Selesai','priority'=>'Biasa','assignee'=>'4 pegawai'],
];
$approvals = [
 ['id'=>1,'title'=>'Penugasan Tim Audit Infrastruktur TI','number'=>'045/ST/LPSI/UMPO/IX/2026','requester'=>'Rina Kusuma','unit'=>'LPSI','submitted'=>'15 Sep 2026 · 09:24','stage'=>'Persetujuan Rektor','sla'=>'1j 36m','priority'=>'Segera'],
 ['id'=>2,'title'=>'Penetapan Panitia Dies Natalis ke-40','number'=>'044/SK/UMPO/IX/2026','requester'=>'Dwi Rahmawati','unit'=>'Biro Umum','submitted'=>'14 Sep 2026 · 15:10','stage'=>'Pemeriksaan Administrasi','sla'=>'18j 12m','priority'=>'Penting'],
 ['id'=>3,'title'=>'Laporan Evaluasi Pembelajaran Agustus','number'=>'039/LAP/LPM/IX/2026','requester'=>'Anisa Putri','unit'=>'LPM','submitted'=>'14 Sep 2026 · 11:42','stage'=>'Persetujuan Wakil Rektor','sla'=>'22j 40m','priority'=>'Biasa'],
];
$assignments = [
 ['title'=>'Audit Infrastruktur TI','number'=>'045/ST/LPSI/UMPO/IX/2026','date'=>'18–20 Sep 2026','location'=>'Gedung Terpadu Lt. 3','progress'=>35,'status'=>'Dalam Pelaksanaan','team'=>6],
 ['title'=>'Pendampingan Akreditasi Prodi','number'=>'042/ST/BAAK/IX/2026','date'=>'16 Sep 2026','location'=>'Ruang Rapat Fakultas','progress'=>70,'status'=>'Dalam Pelaksanaan','team'=>8],
 ['title'=>'Pemutakhiran Data Akademik','number'=>'041/ST/LPSI/UMPO/IX/2026','date'=>'10–12 Sep 2026','location'=>'LPSI','progress'=>100,'status'=>'Selesai','team'=>4],
];
$notifications = [
 ['icon'=>'ri-checkbox-circle-line','tone'=>'amber','title'=>'Surat menunggu persetujuan','text'=>'Penugasan Tim Audit Infrastruktur TI memerlukan tindakan Anda.','time'=>'8 menit lalu','unread'=>true],
 ['icon'=>'ri-edit-box-line','tone'=>'rose','title'=>'Dokumen perlu revisi','text'=>'Perbaiki periode laporan dan lampiran pendukung.','time'=>'1 jam lalu','unread'=>true],
 ['icon'=>'ri-calendar-event-line','tone'=>'violet','title'=>'Agenda dimulai besok','text'=>'Pendampingan Akreditasi Program Studi pukul 08.00 WIB.','time'=>'3 jam lalu','unread'=>false],
];
