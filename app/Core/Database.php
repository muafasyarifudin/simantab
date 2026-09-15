<?php
declare(strict_types=1);
final class Database {
    private static ?PDO $pdo = null;
    public static function connection(): PDO {
        if (self::$pdo) return self::$pdo;
        $host = getenv('SIMANTAP_DB_HOST') ?: '127.0.0.1';
        $port = getenv('SIMANTAP_DB_PORT') ?: '3306';
        $name = getenv('SIMANTAP_DB_NAME') ?: 'db_simantab';
        $user = getenv('SIMANTAP_DB_USER') ?: 'root';
        $pass = getenv('SIMANTAP_DB_PASS') ?: '';
        self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return self::$pdo;
    }
    public static function transaction(callable $callback): mixed {
        $db=self::connection(); $db->beginTransaction();
        try { $result=$callback($db); $db->commit(); return $result; }
        catch(Throwable $e) { if($db->inTransaction()) $db->rollBack(); throw $e; }
    }
}
