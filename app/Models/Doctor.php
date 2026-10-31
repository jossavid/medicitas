<?php
namespace App\Models;
use App\Core\Database;

class Doctor
{
    public static function all(): array { return Database::fetchAll('SELECT d.*, s.name AS specialty_name FROM doctors d JOIN specialties s ON s.id=d.specialty_id ORDER BY d.name'); }
    public static function active(): array { return Database::fetchAll('SELECT d.*, s.name AS specialty_name FROM doctors d JOIN specialties s ON s.id=d.specialty_id WHERE d.active=1 ORDER BY d.name'); }
    public static function find(int $id): ?array { return Database::fetch('SELECT d.*, s.name AS specialty_name FROM doctors d JOIN specialties s ON s.id=d.specialty_id WHERE d.id=?', [$id]); }
    public static function create(array $d): int {
        Database::execute('INSERT INTO doctors (specialty_id,name,license_no,phone,email,active) VALUES (?,?,?,?,?,?)', [$d['specialty_id'],$d['name'],$d['license_no'] ?: null,$d['phone'] ?: null,$d['email'] ?: null,$d['active'] ?? 1]);
        return (int)Database::lastInsertId();
    }
    public static function update(int $id,array $d): void { Database::execute('UPDATE doctors SET specialty_id=?,name=?,license_no=?,phone=?,email=?,active=? WHERE id=?', [$d['specialty_id'],$d['name'],$d['license_no'] ?: null,$d['phone'] ?: null,$d['email'] ?: null,$d['active'] ?? 1,$id]); }
    public static function delete(int $id): void { Database::execute('DELETE FROM doctors WHERE id=?', [$id]); }
}
