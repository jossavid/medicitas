<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Appointment;

class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();
        $doctorId = Auth::role() === 'doctor' ? (int)(Auth::user()['doctor_id'] ?? 0) : null;
        $whereDoctor = $doctorId ? ' AND doctor_id=' . $doctorId : '';
        $stats = [
            'today' => (int)(Database::fetch('SELECT COUNT(*) c FROM appointments WHERE appointment_date=?' . $whereDoctor, [date('Y-m-d')])['c'] ?? 0),
            'pending' => (int)(Database::fetch("SELECT COUNT(*) c FROM appointments WHERE status IN ('pending','confirmed')" . $whereDoctor)['c'] ?? 0),
            'patients' => (int)(Database::fetch('SELECT COUNT(*) c FROM patients WHERE active=1')['c'] ?? 0),
            'doctors' => (int)(Database::fetch('SELECT COUNT(*) c FROM doctors WHERE active=1')['c'] ?? 0),
        ];
        $upcoming = Appointment::upcoming($doctorId, 8);
        view('dashboard/index', compact('stats', 'upcoming') + ['title' => 'Dashboard']);
    }
}
