<?php
namespace App\Models;
use App\Core\Database;

class Specialty
{
    public static function all(): array { return Database::fetchAll('SELECT * FROM specialties ORDER BY name'); }
    public static function active(): array { return Database::fetchAll('SELECT * FROM specialties WHERE active=1 ORDER BY name'); }
    public static function find(int $id): ?array { return Database::fetch('SELECT * FROM specialties WHERE id=?', [$id]); }
    public static function create(array $d): int { Database::execute('INSERT INTO specialties (name,description,active) VALUES (?,?,?)', [$d['name'],$d['description'] ?: null,$d['active'] ?? 1]); return (int)Database::lastInsertId(); }
    public static function update(int $id,array $d): void { Database::execute('UPDATE specialties SET name=?,description=?,active=? WHERE id=?', [$d['name'],$d['description'] ?: null,$d['active'] ?? 1,$id]); }
    public static function delete(int $id): void { Database::execute('DELETE FROM specialties WHERE id=?', [$id]); }
}
