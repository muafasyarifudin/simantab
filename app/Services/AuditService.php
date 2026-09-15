<?php
declare(strict_types=1);
final class AuditService {
    public static function log(string $event,?string $type=null,?int $id=null,?array $old=null,?array $new=null,string $severity='info'): void {
        try { $u=$_SESSION['auth_user']??null; $s=Database::connection()->prepare('INSERT INTO audit_logs(institution_id,actor_user_id,event,severity,object_type,object_id,description,old_values,new_values,ip_address,user_agent,request_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
            $s->execute([$u['institution_id']??null,$u['id']??null,$event,$severity,$type,$id,$event,$old?json_encode($old):null,$new?json_encode($new):null,Security::clientIp(),substr($_SERVER['HTTP_USER_AGENT']??'',0,500),$_SERVER['HTTP_X_REQUEST_ID']??bin2hex(random_bytes(16))]);
        } catch(Throwable $e) { error_log('Audit failure: '.$e->getMessage()); }
    }
}
