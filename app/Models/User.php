<?php
namespace App\Models;
use App\Core\Database;

class User
{
    public static function all(): array { return Database::fetchAll('SELECT u.id,u.name,u.email,u.role,u.active,u.doctor_id,u.created_at,d.name AS doctor_name FROM users u LEFT JOIN doctors d ON d.id=u.doctor_id ORDER BY u.name'); }
    public static function find(int $id): ?array { return Database::fetch('SELECT * FROM users WHERE id=?',[$id]); }
    public static function emailExists(string $email,?int $ignoreId=null): bool { $sql='SELECT id FROM users WHERE email=?';$params=[$email]; if($ignoreId){$sql.=' AND id<>?';$params[]=$ignoreId;} return Database::fetch($sql,$params)!==null; }
    public static function create(array $d): int { Database::execute('INSERT INTO users (name,email,password,role,doctor_id,active) VALUES (?,?,?,?,?,?)',[$d['name'],$d['email'],password_hash($d['password'],PASSWORD_DEFAULT),$d['role'],$d['doctor_id'] ?: null,$d['active'] ?? 1]); return (int)Database::lastInsertId(); }
    public static function update(int $id,array $d): void {
        $params=[$d['name'],$d['email'],$d['role'],$d['doctor_id'] ?: null,$d['active'] ?? 1];
        $sql='UPDATE users SET name=?,email=?,role=?,doctor_id=?,active=?';
        if(!empty($d['password'])){$sql.=',password=?';$params[]=password_hash($d['password'],PASSWORD_DEFAULT);} $sql.=' WHERE id=?';$params[]=$id;
        Database::execute($sql,$params);
    }
    public static function delete(int $id): void { Database::execute('DELETE FROM users WHERE id=?',[$id]); }
}
