<?php
declare(strict_types=1);
final class AuthService {
    public static function attempt(string $identity,string $password): bool {
        $db=Database::connection(); $ip=Security::clientIp();
        $q=$db->prepare('SELECT COUNT(*) FROM login_attempts WHERE username=? AND ip_address=? AND was_successful=0 AND attempted_at>DATE_SUB(NOW(),INTERVAL 15 MINUTE)'); $q->execute([$identity,$ip]);
        if((int)$q->fetchColumn()>=5) throw new RuntimeException('Terlalu banyak percobaan. Coba lagi 15 menit.',429);
        $q=$db->prepare("SELECT u.*,e.full_name,r.name role_name,ur.unit_id FROM users u LEFT JOIN employees e ON e.id=u.employee_id LEFT JOIN user_roles ur ON ur.user_id=u.id LEFT JOIN roles r ON r.id=ur.role_id WHERE (u.username=? OR u.email=?) AND u.deleted_at IS NULL LIMIT 1"); $q->execute([$identity,$identity]); $user=$q->fetch();
        $ok=$user && $user['status']==='active' && password_verify($password,$user['password_hash']);
        $a=$db->prepare('INSERT INTO login_attempts(username,user_id,ip_address,user_agent,was_successful,failure_reason) VALUES(?,?,?,?,?,?)'); $a->execute([$identity,$user['id']??null,$ip,substr($_SERVER['HTTP_USER_AGENT']??'',0,500),$ok?1:0,$ok?null:'invalid_credentials']);
        if(!$ok){ if($user){$db->prepare('UPDATE users SET failed_login_count=failed_login_count+1 WHERE id=?')->execute([$user['id']]);} return false; }
        session_regenerate_id(true); $_SESSION['auth_user']=['id'=>(int)$user['id'],'institution_id'=>(int)$user['institution_id'],'unit_id'=>$user['unit_id']?(int)$user['unit_id']:null,'name'=>$user['full_name']?:$user['username'],'role'=>$user['role_name']?:'Pengguna','must_change_password'=>(bool)$user['must_change_password']];
        $db->prepare('UPDATE users SET failed_login_count=0,last_login_at=NOW(),last_login_ip=? WHERE id=?')->execute([$ip,$user['id']]); AuditService::log('auth.login','user',(int)$user['id']); return true;
    }
    public static function user(): ?array { return $_SESSION['auth_user']??null; }
    public static function requireUser(): array { $u=self::user(); if(!$u) throw new RuntimeException('Autentikasi diperlukan.',401); return $u; }
    public static function logout(): void { if(self::user())AuditService::log('auth.logout','user',self::user()['id']); $_SESSION=[]; if(ini_get('session.use_cookies'))setcookie(session_name(),'',time()-42000,'/'); session_destroy(); }
}
