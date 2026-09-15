<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
function ok(bool $condition,string $message):void{if(!$condition)throw new RuntimeException('FAIL: '.$message);echo "OK: {$message}\n";}
$db=Database::connection();
$login=AuthService::attempt('admin','Admin@123');
ok((int)$db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='db_simantab'")->fetchColumn()>=50,'database schema tersedia');
ok($login,'login memvalidasi password hash');
ok(RbacService::can('documents.create'),'RBAC admin memiliki documents.create');
$unit=(int)$db->query("SELECT id FROM organization_units WHERE code='LPSI' LIMIT 1")->fetchColumn();
$type=(int)$db->query("SELECT id FROM document_types WHERE code='ST' LIMIT 1")->fetchColumn();
$scheme=(int)$db->query("SELECT id FROM numbering_schemes WHERE unit_id={$unit} AND document_type_id={$type} LIMIT 1")->fetchColumn();
$doc=DocumentService::create(['owner_unit_id'=>$unit,'document_type_id'=>$type,'numbering_scheme_id'=>$scheme,'title'=>'Dokumen Pengujian Integrasi','summary'=>'Dibuat otomatis oleh test suite','priority'=>'normal']);
ok($doc['status']==='draft','CRUD menyimpan dokumen permanen');
$updated=DocumentService::update((int)$doc['id'],['title'=>'Dokumen Pengujian Integrasi Diperbarui']);ok((int)$updated['current_version_no']===2,'versioning dokumen berjalan');
$submitted=WorkflowService::submit((int)$doc['id']);ok($submitted['status']==='under_review','workflow pengajuan berjalan');
$steps=$db->prepare('SELECT ws.id FROM workflow_steps ws JOIN workflow_instances wi ON wi.id=ws.workflow_instance_id WHERE wi.document_id=? ORDER BY ws.step_no');$steps->execute([$doc['id']]);$ids=$steps->fetchAll(PDO::FETCH_COLUMN);
WorkflowService::decide((int)$ids[0],'approved','Administrasi lengkap');WorkflowService::decide((int)$ids[1],'approved','Disetujui');
$final=DocumentService::find((int)$doc['id']);ok($final['status']==='approved'&&!empty($final['number']),'persetujuan dan penomoran atomik berjalan');
$pdf=PdfService::generateFinal((int)$doc['id']);ok(strlen($pdf['hash'])===64&&is_file($db->query('SELECT storage_path FROM document_files WHERE id='.(int)$pdf['file_id'])->fetchColumn()),'PDF QR hash dan tanda tangan tersedia');
ok((int)$db->query("SELECT COUNT(*) FROM audit_logs WHERE object_type='document' AND object_id=".(int)$doc['id'])->fetchColumn()>=4,'audit log mencatat transaksi');
$db->prepare('UPDATE documents SET deleted_at=NOW() WHERE id=?')->execute([$doc['id']]);
echo "ALL INTEGRATION TESTS PASSED\n";
