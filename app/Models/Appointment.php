<?php
namespace App\Models;
use App\Core\Database;

class Appointment
{
    public static function all(?int $doctorId=null): array {
        $sql="SELECT a.*, p.first_name || ' ' || p.last_name AS patient_name, d.name AS doctor_name, s.name AS specialty_name FROM appointments a JOIN patients p ON p.id=a.patient_id JOIN doctors d ON d.id=a.doctor_id JOIN specialties s ON s.id=d.specialty_id";
        $params=[];
        if($doctorId){$sql.=' WHERE a.doctor_id=?';$params[]=$doctorId;}
        $sql.=' ORDER BY a.appointment_date DESC, a.appointment_time DESC';
        if(app_config()['db']['driver']==='mysql') $sql=str_replace("p.first_name || ' ' || p.last_name","CONCAT(p.first_name, ' ', p.last_name)",$sql);
        return Database::fetchAll($sql,$params);
    }
    public static function byPatient(int $patientId): array
    {
        $sql = "
            SELECT
                a.*,
                d.name AS doctor_name,
                s.name AS specialty_name
            FROM appointments a
            JOIN doctors d
                ON d.id = a.doctor_id
            JOIN specialties s
                ON s.id = d.specialty_id
            WHERE a.patient_id = ?
            ORDER BY
                a.appointment_date DESC,
                a.appointment_time DESC
        ";

        return Database::fetchAll($sql, [$patientId]);
    }
    public static function upcoming(?int $doctorId=null,int $limit=8): array {
        $concat=app_config()['db']['driver']==='mysql' ? "CONCAT(p.first_name, ' ', p.last_name)" : "p.first_name || ' ' || p.last_name";
        $sql="SELECT a.*, $concat AS patient_name, d.name AS doctor_name FROM appointments a JOIN patients p ON p.id=a.patient_id JOIN doctors d ON d.id=a.doctor_id WHERE a.appointment_date >= ? AND a.status NOT IN ('cancelled','completed')";
        $params=[date('Y-m-d')];
        if($doctorId){$sql.=' AND a.doctor_id=?';$params[]=$doctorId;}
        $sql.=' ORDER BY a.appointment_date, a.appointment_time LIMIT '.(int)$limit;
        return Database::fetchAll($sql,$params);
    }
    public static function find(int $id): ?array { return Database::fetch('SELECT * FROM appointments WHERE id=?',[$id]); }
    public static function create(array $d): int {
        Database::execute('INSERT INTO appointments (patient_id,doctor_id,appointment_date,appointment_time,duration_minutes,reason,status,notes,created_by) VALUES (?,?,?,?,?,?,?,?,?)',[$d['patient_id'],$d['doctor_id'],$d['appointment_date'],$d['appointment_time'],$d['duration_minutes'],$d['reason'],$d['status'],$d['notes'] ?: null,$d['created_by']]);
        return (int)Database::lastInsertId();
    }
    public static function update(int $id,array $d): void { Database::execute('UPDATE appointments SET patient_id=?,doctor_id=?,appointment_date=?,appointment_time=?,duration_minutes=?,reason=?,status=?,notes=?,updated_at=CURRENT_TIMESTAMP WHERE id=?',[$d['patient_id'],$d['doctor_id'],$d['appointment_date'],$d['appointment_time'],$d['duration_minutes'],$d['reason'],$d['status'],$d['notes'] ?: null,$id]); }
    public static function delete(int $id): void { Database::execute('DELETE FROM appointments WHERE id=?',[$id]); }
    public static function hasConflict(int $doctorId,string $date,string $time,?int $ignoreId=null): bool {
        $sql='SELECT id FROM appointments WHERE doctor_id=? AND appointment_date=? AND appointment_time=? AND status != ?';
        $params=[$doctorId,$date,$time,'cancelled'];
        if($ignoreId){$sql.=' AND id<>?';$params[]=$ignoreId;}
        return Database::fetch($sql,$params)!==null;
    }
}
