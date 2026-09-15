<?php
declare(strict_types=1);
final class RbacService {
    private static array $cache=[];
    public static function can(string $permission,?int $unitId=null): bool {
        $u=AuthService::user(); if(!$u)return false; $key=$u['id'].'|'.$permission.'|'.($unitId??0); if(isset(self::$cache[$key]))return self::$cache[$key];
        $sql='SELECT COUNT(*) FROM user_roles ur JOIN role_permissions rp ON rp.role_id=ur.role_id JOIN permissions p ON p.id=rp.permission_id WHERE ur.user_id=? AND p.code=? AND (ur.valid_from IS NULL OR ur.valid_from<=CURDATE()) AND (ur.valid_until IS NULL OR ur.valid_until>=CURDATE()) AND (ur.unit_id IS NULL OR ur.unit_id=?)';
        $q=Database::connection()->prepare($sql); $q->execute([$u['id'],$permission,$unitId??$u['unit_id']]); return self::$cache[$key]=(bool)$q->fetchColumn();
    }
    public static function authorize(string $permission,?int $unitId=null): void { if(!self::can($permission,$unitId))throw new RuntimeException('Anda tidak memiliki izin untuk tindakan ini.',403); }
    public static function unitScopeSql(string $alias='d'): array { $u=AuthService::requireUser(); if(self::can('users.manage',null))return ["{$alias}.institution_id=?",[$u['institution_id']]]; return ["{$alias}.institution_id=? AND {$alias}.owner_unit_id=?",[$u['institution_id'],$u['unit_id']]]; }
}
