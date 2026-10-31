<?php
namespace App\Models;
use App\Core\Database;

class Patient
{
    public static function all(): array { return Database::fetchAll('SELECT * FROM patients ORDER BY last_name, first_name'); }
    public static function active(): array { return Database::fetchAll('SELECT * FROM patients WHERE active = 1 ORDER BY last_name, first_name'); }
    public static function find(int $id): ?array { return Database::fetch('SELECT * FROM patients WHERE id = ?', [$id]); }
    public static function create(array $d): int {
        Database::execute('INSERT INTO patients (first_name,last_name,birth_date,sex,phone,email,address,notes,active) VALUES (?,?,?,?,?,?,?,?,?)', [
            $d['first_name'],$d['last_name'],$d['birth_date'] ?: null,$d['sex'] ?: null,$d['phone'] ?: null,$d['email'] ?: null,$d['address'] ?: null,$d['notes'] ?: null,$d['active'] ?? 1
        ]);
        return (int)Database::lastInsertId();
    }
    public static function update(int $id, array $d): void {
        Database::execute('UPDATE patients SET first_name=?,last_name=?,birth_date=?,sex=?,phone=?,email=?,address=?,notes=?,active=? WHERE id=?', [
            $d['first_name'],$d['last_name'],$d['birth_date'] ?: null,$d['sex'] ?: null,$d['phone'] ?: null,$d['email'] ?: null,$d['address'] ?: null,$d['notes'] ?: null,$d['active'] ?? 1,$id
        ]);
    }
    public static function delete(int $id): void { Database::execute('DELETE FROM patients WHERE id=?', [$id]); }
}
