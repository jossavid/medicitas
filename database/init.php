<?php
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$pdo = Database::connection();
$driver = app_config()['db']['driver'];
$schemaFile = __DIR__ . '/schema_' . ($driver === 'mysql' ? 'mysql' : 'sqlite') . '.sql';
$sql = file_get_contents($schemaFile);
$pdo->exec($sql);

$specialties = [
    ['Medicina General', 'Atención primaria y control general.'],
    ['Cardiología', 'Evaluación y seguimiento cardiovascular.'],
    ['Pediatría', 'Atención médica de niños y adolescentes.'],
    ['Ginecología', 'Atención integral de salud femenina.'],
];
$stmt = $pdo->prepare('INSERT INTO specialties (name, description) VALUES (?, ?)');
foreach ($specialties as $s) $stmt->execute($s);

$pdo->exec("INSERT INTO doctors (specialty_id, name, license_no, phone, email) VALUES
(1, 'Dra. Laura Méndez', 'COL-10452', '5555-1001', 'laura@medicitas.test'),
(2, 'Dr. Carlos López', 'COL-11807', '5555-1002', 'carlos@medicitas.test'),
(3, 'Dra. Ana Morales', 'COL-12321', '5555-1003', 'ana@medicitas.test')");

$pdo->exec("INSERT INTO patients (first_name,last_name,birth_date,sex,phone,email,address,notes) VALUES
('Juan','Pérez','1993-05-16','Masculino','5555-2201','juan@example.com','Zacapa','Paciente de control'),
('María','López','1988-11-08','Femenino','5555-2202','maria@example.com','Zacapa','Sin observaciones'),
('Sofía','Ramírez','2015-02-14','Femenino','5555-2203','sofia@example.com','Estanzuela','Acompañada por responsable')");

$users = [
    ['Administrador', 'admin@medicitas.test', 'Admin123!', 'admin', null],
    ['Recepción', 'recepcion@medicitas.test', 'Recep123!', 'receptionist', null],
    ['Dr. Carlos López', 'medico@medicitas.test', 'Medico123!', 'doctor', 2],
];
$stmt = $pdo->prepare('INSERT INTO users (name,email,password,role,doctor_id) VALUES (?,?,?,?,?)');
foreach ($users as [$name,$email,$pass,$role,$doctorId]) {
    $stmt->execute([$name,$email,password_hash($pass, PASSWORD_DEFAULT),$role,$doctorId]);
}

$pdo->exec("INSERT INTO schedules (doctor_id, weekday, start_time, end_time, slot_minutes) VALUES
(1,1,'08:00','12:00',30),(1,3,'08:00','12:00',30),(1,5,'08:00','12:00',30),
(2,2,'09:00','13:00',30),(2,4,'14:00','18:00',30),
(3,1,'13:00','17:00',30),(3,3,'13:00','17:00',30)");

$today = new DateTimeImmutable('today');
$future1 = $today->modify('+1 day')->format('Y-m-d');
$future2 = $today->modify('+2 day')->format('Y-m-d');
$stmt = $pdo->prepare('INSERT INTO appointments (patient_id,doctor_id,appointment_date,appointment_time,duration_minutes,reason,status,notes,created_by) VALUES (?,?,?,?,?,?,?,?,?)');
$stmt->execute([1,2,$future1,'09:00',30,'Evaluación cardiológica','confirmed','Control programado',1]);
$stmt->execute([2,1,$future2,'10:00',30,'Consulta general','pending','',1]);

echo "Base de datos inicializada correctamente ($driver).\n";
echo "Admin: admin@medicitas.test / Admin123!\n";
echo "Recepción: recepcion@medicitas.test / Recep123!\n";
echo "Médico: medico@medicitas.test / Medico123!\n";
