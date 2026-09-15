<?php
declare(strict_types=1);
final class Security {
    public static function startSession(): void {
        $path=dirname(__DIR__,2).'/storage/sessions';
        if(!is_dir($path)) mkdir($path,0770,true);
        session_name('SIMANTAPSESSID'); session_save_path($path);
        ini_set('session.use_strict_mode','1'); ini_set('session.cookie_httponly','1'); ini_set('session.cookie_samesite','Lax');
        if(!headers_sent()) session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax','path'=>'/']);
        if(session_status()!==PHP_SESSION_ACTIVE) session_start();
    }
    public static function csrfToken(): string {
        if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    public static function verifyCsrf(?string $token): void {
        if(!$token || !hash_equals($_SESSION['csrf_token']??'', $token)) throw new RuntimeException('Token keamanan tidak valid.',419);
    }
    public static function clientIp(): string { return substr($_SERVER['REMOTE_ADDR']??'0.0.0.0',0,45); }
    public static function jsonInput(): array {
        $raw=file_get_contents('php://input'); $data=json_decode($raw?:'{}',true);
        if(!is_array($data)) throw new InvalidArgumentException('JSON tidak valid.'); return $data;
    }
}
