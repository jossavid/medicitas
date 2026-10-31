<?php
namespace App\Models;
use App\Core\Database;

class Schedule
{
    public static function all(): array { return Database::fetchAll('SELECT sc.*, d.name AS doctor_name FROM schedules sc JOIN doctors d ON d.id=sc.doctor_id ORDER BY d.name, sc.weekday, sc.start_time'); }
    public static function find(int $id): ?array { return Database::fetch('SELECT * FROM schedules WHERE id=?', [$id]); }
    public static function create(array $d): int { Database::execute('INSERT INTO schedules (doctor_id,weekday,start_time,end_time,slot_minutes,active) VALUES (?,?,?,?,?,?)', [$d['doctor_id'],$d['weekday'],$d['start_time'],$d['end_time'],$d['slot_minutes'],$d['active'] ?? 1]); return (int)Database::lastInsertId(); }
    public static function update(int $id,array $d): void { Database::execute('UPDATE schedules SET doctor_id=?,weekday=?,start_time=?,end_time=?,slot_minutes=?,active=? WHERE id=?', [$d['doctor_id'],$d['weekday'],$d['start_time'],$d['end_time'],$d['slot_minutes'],$d['active'] ?? 1,$id]); }
    public static function delete(int $id): void { Database::execute('DELETE FROM schedules WHERE id=?', [$id]); }
    public static function matches(int $doctorId,string $date,string $time,int $duration=30): bool {
        $weekday=(int)(new \DateTimeImmutable($date))->format('N');
        $rows=Database::fetchAll('SELECT * FROM schedules WHERE doctor_id=? AND weekday=? AND active=1',[$doctorId,$weekday]);
        $start=strtotime($date.' '.$time); $end=$start+$duration*60;
        foreach($rows as $r){ $rs=strtotime($date.' '.$r['start_time']); $re=strtotime($date.' '.$r['end_time']); if($start >= $rs && $end <= $re) return true; }
        return false;
    }
}
