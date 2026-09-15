<?php
declare(strict_types=1);
ob_start();
require dirname(__DIR__).'/bootstrap.php';
function assertTrue(bool $condition,string $message):void{if(!$condition)throw new RuntimeException('FAIL: '.$message);echo "OK: {$message}\n";}
$failed=!AuthService::attempt('admin','wrong-password');assertTrue($failed,'password salah ditolak');
$csrfRejected=false;try{Security::verifyCsrf('invalid');}catch(Throwable){$csrfRejected=true;}assertTrue($csrfRejected,'CSRF tidak valid ditolak');
assertTrue(AuthService::attempt('admin','Admin@123'),'password hash valid diterima');
assertTrue(strlen(Security::csrfToken())===64,'token CSRF kriptografis tersedia');
$db=Database::connection();$before=(int)$db->query('SELECT last_number FROM numbering_sequences ORDER BY id LIMIT 1')->fetchColumn();assertTrue($before>=1,'sequence penomoran tersimpan permanen');
$private=dirname(__DIR__).'/storage/.htaccess';assertTrue(is_file($private)&&str_contains(file_get_contents($private),'Require all denied'),'storage privat ditolak oleh web server');
echo "ALL SECURITY TESTS PASSED\n";
ob_end_flush();
