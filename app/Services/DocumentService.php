<?php
declare(strict_types=1);
final class DocumentService {
    public static function list(array $filters=[]): array {
        [$scope,$params]=RbacService::unitScopeSql('d'); $where=[$scope,'d.deleted_at IS NULL'];
        if(!empty($filters['status'])){$where[]='d.status=?';$params[]=$filters['status'];}
        if(!empty($filters['q'])){$where[]='(d.title LIKE ? OR d.number LIKE ?)';$params[]='%'.$filters['q'].'%';$params[]='%'.$filters['q'].'%';}
        $sql='SELECT d.*,dt.name type_name,ou.short_name unit_name,e.full_name signer_name FROM documents d JOIN document_types dt ON dt.id=d.document_type_id JOIN organization_units ou ON ou.id=d.owner_unit_id LEFT JOIN employees e ON e.id=d.signer_employee_id WHERE '.implode(' AND ',$where).' ORDER BY d.created_at DESC LIMIT 100';
        $q=Database::connection()->prepare($sql);$q->execute($params);return $q->fetchAll();
    }
    public static function find(int $id): array {
        [$scope,$params]=RbacService::unitScopeSql('d');array_unshift($params,$id);
        $q=Database::connection()->prepare('SELECT d.*,dt.name type_name,ou.name unit_name FROM documents d JOIN document_types dt ON dt.id=d.document_type_id JOIN organization_units ou ON ou.id=d.owner_unit_id WHERE d.id=? AND '.$scope.' AND d.deleted_at IS NULL');$q->execute($params);$d=$q->fetch();if(!$d)throw new RuntimeException('Dokumen tidak ditemukan.',404);return $d;
    }
    public static function create(array $data): array {
        RbacService::authorize('documents.create',(int)($data['owner_unit_id']??0));$u=AuthService::requireUser();
        foreach(['owner_unit_id','document_type_id','title'] as $f)if(empty($data[$f]))throw new InvalidArgumentException("Field {$f} wajib diisi.");
        return Database::transaction(function(PDO $db)use($data,$u){
            $num = !empty($data['number']) ? trim((string)$data['number']) : null;
            $s=$db->prepare('INSERT INTO documents(institution_id,owner_unit_id,creator_id,document_type_id,template_version_id,numbering_scheme_id,archive_classification_id,number,title,summary,confidentiality,priority,status,document_date,effective_from,effective_until,report_due_at,report_type,signer_employee_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $s->execute([$u['institution_id'],$data['owner_unit_id'],$u['id'],$data['document_type_id'],$data['template_version_id']??null,$data['numbering_scheme_id']??null,$data['archive_classification_id']??null,$num,trim($data['title']),$data['summary']??null,$data['confidentiality']??'normal',$data['priority']??'normal','draft',$data['document_date']??date('Y-m-d'),$data['effective_from']??null,$data['effective_until']??null,$data['report_due_at']??null,$data['report_type']??'none',$data['signer_employee_id']??null]);
            $id=(int)$db->lastInsertId();$db->prepare('INSERT INTO document_versions(document_id,version_no,content_json,created_by) VALUES(?,1,?,?)')->execute([$id,json_encode($data['content']??$data,JSON_UNESCAPED_UNICODE),$u['id']]);AuditService::log('document.created','document',$id,null,$data);return self::find($id);
        });
    }
    public static function update(int $id,array $data): array {
        $old=self::find($id);RbacService::authorize('documents.update',(int)$old['owner_unit_id']);if(!in_array($old['status'],['draft','revision'],true))throw new RuntimeException('Hanya draf atau dokumen revisi yang dapat diubah.',409);$u=AuthService::requireUser();
        return Database::transaction(function(PDO $db)use($id,$data,$old,$u){$v=(int)$old['current_version_no']+1;$num=!empty($data['number'])?trim((string)$data['number']):$old['number'];$db->prepare('UPDATE documents SET number=?,title=?,summary=?,priority=?,confidentiality=?,document_date=?,current_version_no=? WHERE id=?')->execute([$num,$data['title']??$old['title'],$data['summary']??$old['summary'],$data['priority']??$old['priority'],$data['confidentiality']??$old['confidentiality'],$data['document_date']??$old['document_date'],$v,$id]);$db->prepare('INSERT INTO document_versions(document_id,version_no,content_json,change_summary,created_by) VALUES(?,?,?,?,?)')->execute([$id,$v,json_encode($data['content']??$data,JSON_UNESCAPED_UNICODE),$data['change_summary']??'Pembaruan draf',$u['id']]);AuditService::log('document.updated','document',$id,$old,$data);return self::find($id);});
    }
    public static function delete(int $id): void {$d=self::find($id);RbacService::authorize('documents.cancel',(int)$d['owner_unit_id']);if($d['status']!=='draft')throw new RuntimeException('Dokumen yang sudah diajukan tidak dapat dihapus.',409);Database::connection()->prepare('UPDATE documents SET deleted_at=NOW(),status="cancelled",cancelled_at=NOW(),cancellation_reason="Dihapus pembuat" WHERE id=?')->execute([$id]);AuditService::log('document.deleted','document',$id,$d,null,'warning');}
}
