USE db_simantab;
START TRANSACTION;

INSERT IGNORE INTO institutions(code,name,short_name,address,email,website)
VALUES('UMPO','Universitas Muhammadiyah Ponorogo','UMPO','Jl. Budi Utomo No. 10, Ponorogo','info@umpo.ac.id','https://umpo.ac.id');
SET @institution_id=(SELECT id FROM institutions WHERE code='UMPO' LIMIT 1);

INSERT IGNORE INTO organization_units(institution_id,parent_id,code,name,short_name,unit_type,level_no) VALUES
(@institution_id,NULL,'REKTORAT','Rektorat','Rektorat','unit_utama',1),
(@institution_id,NULL,'LPSI','Lembaga Pengembangan Sistem Informasi','LPSI','lembaga',1),
(@institution_id,NULL,'BAAK','Biro Administrasi Akademik dan Kemahasiswaan','BAAK','biro',1),
(@institution_id,NULL,'LPPM','Lembaga Penelitian dan Pengabdian kepada Masyarakat','LPPM','lembaga',1),
(@institution_id,NULL,'LPM','Lembaga Penjaminan Mutu','LPM','lembaga',1),
(@institution_id,NULL,'BIRO-UMUM','Biro Administrasi Umum','Biro Umum','biro',1);
SET @lpsi_id=(SELECT id FROM organization_units WHERE institution_id=@institution_id AND code='LPSI');

INSERT IGNORE INTO positions(institution_id,code,name,level_no,is_structural) VALUES
(@institution_id,'REKTOR','Rektor',1,1),(@institution_id,'WAREK-1','Wakil Rektor I',2,1),
(@institution_id,'KEPALA-UNIT','Kepala Satuan Kerja',3,1),(@institution_id,'KABAG-TU','Kepala Bagian Tata Usaha',4,1),
(@institution_id,'STAF','Staf/Pegawai',9,0);

INSERT IGNORE INTO employees(institution_id,employee_no,full_name,back_title,email,employment_status)
VALUES(@institution_id,'198907102019031004','Ahmad Fauzi','S.Kom.','ahmad.fauzi@umpo.ac.id','Tetap');
SET @employee_id=(SELECT id FROM employees WHERE institution_id=@institution_id AND employee_no='198907102019031004');

INSERT IGNORE INTO users(institution_id,employee_id,username,email,password_hash,status,must_change_password)
VALUES(@institution_id,@employee_id,'admin','ahmad.fauzi@umpo.ac.id','$2y$10$qVBmv.RXg0xfS.baS76YPenkFO6gs1WyCMSU02S.Y5rkCabilURvO','active',1);
SET @admin_id=(SELECT id FROM users WHERE institution_id=@institution_id AND username='admin');

INSERT IGNORE INTO roles(institution_id,code,name,description,is_system) VALUES
(NULL,'super_admin','Super Admin','Akses global seluruh instansi',1),
(@institution_id,'admin_institution','Admin Instansi','Mengelola konfigurasi satu instansi',1),
(@institution_id,'admin_unit','Admin Satuan Kerja','Mengelola data pada satu unit',1),
(@institution_id,'administration','Tata Usaha','Verifikasi, penomoran, distribusi, dan arsip',1),
(@institution_id,'legal','Bagian Hukum','Pemeriksaan aspek hukum',1),
(@institution_id,'leader','Pimpinan','Persetujuan dan penandatanganan',1),
(@institution_id,'unit_head','Kepala Satuan Kerja','Pengajuan dan pemantauan unit',1),
(@institution_id,'document_creator','Pembuat Surat','Membuat dan memperbaiki draf',1),
(@institution_id,'employee','Pegawai','Menerima tugas dan membuat laporan',1),
(@institution_id,'auditor','Auditor','Membaca arsip dan audit log',1);

INSERT IGNORE INTO permissions(code,name,module) VALUES
('dashboard.view','Melihat dashboard','dashboard'),('users.manage','Mengelola pengguna','users'),
('units.manage','Mengelola unit','organization'),('roles.manage','Mengelola peran dan izin','rbac'),
('documents.view','Melihat dokumen','documents'),('documents.create','Membuat dokumen','documents'),
('documents.update','Mengubah draf dokumen','documents'),('documents.submit','Mengajukan dokumen','documents'),
('documents.cancel','Membatalkan dokumen','documents'),('documents.secret.view','Melihat dokumen rahasia','documents'),
('approvals.view','Melihat antrean persetujuan','workflow'),('approvals.decide','Memberi keputusan persetujuan','workflow'),
('numbering.manage','Mengelola dan menerbitkan nomor','numbering'),('templates.manage','Mengelola template','templates'),
('documents.sign','Menandatangani dokumen','signature'),('documents.distribute','Mendistribusikan dokumen','distribution'),
('dispositions.manage','Membuat dan memproses disposisi','disposition'),('agendas.view','Melihat agenda','agenda'),
('reports.create','Membuat laporan pelaksanaan','reports'),('reports.review','Memeriksa laporan','reports'),
('archives.view','Mencari arsip','archive'),('archives.export','Mengekspor arsip','archive'),
('audit.view','Melihat audit log','audit'),('audit.export','Mengekspor audit log','audit'),
('settings.manage','Mengelola konfigurasi sistem','settings');

SET @admin_role=(SELECT id FROM roles WHERE institution_id=@institution_id AND code='admin_institution');
INSERT IGNORE INTO role_permissions(role_id,permission_id) SELECT @admin_role,id FROM permissions;
INSERT IGNORE INTO user_roles(user_id,role_id,institution_id,unit_id,confidentiality_scope,created_by)
VALUES(@admin_id,@admin_role,@institution_id,@lpsi_id,'all',@admin_id);

INSERT IGNORE INTO document_types(institution_id,code,name,requires_assignment,requires_report,requires_legal_review,sort_order) VALUES
(@institution_id,'ST','Surat Tugas',1,1,0,1),(@institution_id,'SK','Surat Keputusan',0,0,1,2),
(@institution_id,'LAP','Surat Laporan Antar-Satuan Kerja',0,0,0,3),(@institution_id,'ND','Nota Dinas',0,0,0,4),
(@institution_id,'UND','Surat Undangan',0,0,0,5),(@institution_id,'SE','Surat Edaran',0,0,0,6),
(@institution_id,'SPRIN','Surat Perintah',1,1,0,7),(@institution_id,'SKET','Surat Keterangan',0,0,0,8),
(@institution_id,'BA','Berita Acara',0,0,0,9);

INSERT IGNORE INTO archive_classifications(institution_id,code,name,retention_active_years,retention_inactive_years,final_action) VALUES
(@institution_id,'UM.01','Administrasi Umum',2,3,'review'),(@institution_id,'KP.02','Kepegawaian',5,5,'review'),
(@institution_id,'TI.01','Teknologi Informasi',3,2,'review'),(@institution_id,'HK.01','Hukum dan Keputusan',5,5,'permanent');

SET @st_id=(SELECT id FROM document_types WHERE institution_id=@institution_id AND code='ST');
INSERT IGNORE INTO numbering_schemes(institution_id,unit_id,document_type_id,name,format_pattern,reset_period,padding_length,assign_stage)
VALUES(@institution_id,@lpsi_id,@st_id,'Surat Tugas LPSI','{SEQUENCE}/ST/{UNIT}/{INSTITUTION}/{ROMAN_MONTH}/{YEAR}','yearly',3,'signature');

INSERT INTO workflow_definitions(institution_id,unit_id,document_type_id,name,description,version_no,is_active,created_by)
SELECT @institution_id,@lpsi_id,@st_id,'Alur Surat Tugas LPSI','Pemeriksaan administrasi dan persetujuan pimpinan',1,1,@admin_id
WHERE NOT EXISTS(SELECT 1 FROM workflow_definitions WHERE institution_id=@institution_id AND unit_id=@lpsi_id AND document_type_id=@st_id AND version_no=1);
SET @workflow_id=(SELECT id FROM workflow_definitions WHERE institution_id=@institution_id AND unit_id=@lpsi_id AND document_type_id=@st_id ORDER BY version_no DESC LIMIT 1);
INSERT IGNORE INTO workflow_definition_steps(workflow_definition_id,step_no,name,action_type,execution_mode,approver_type,approver_reference_id,minimum_approvals,sla_minutes)
VALUES(@workflow_id,1,'Pemeriksaan Administrasi','verification','serial','role',@admin_role,1,1440),
(@workflow_id,2,'Persetujuan dan Pengesahan','approval','serial','role',@admin_role,1,2880);

INSERT IGNORE INTO system_settings(institution_id,setting_key,setting_value,value_type,is_public,updated_by) VALUES
(@institution_id,'app.name','SIMANTAP','string',1,@admin_id),(@institution_id,'app.timezone','Asia/Jakarta','string',1,@admin_id),
(@institution_id,'upload.max_size_mb','20','integer',0,@admin_id),(@institution_id,'security.session_timeout_minutes','30','integer',0,@admin_id),
(@institution_id,'notifications.email_enabled','true','boolean',0,@admin_id);

COMMIT;
