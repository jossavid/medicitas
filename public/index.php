<?php
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PatientController;
use App\Controllers\SpecialtyController;
use App\Controllers\DoctorController;
use App\Controllers\ScheduleController;
use App\Controllers\AppointmentController;
use App\Controllers\UserController;
use App\Controllers\PatientFileController;

$router = new Router();
$router->get('/', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/patients', [PatientController::class, 'index']);
$router->get('/patients/create', [PatientController::class, 'create']);
$router->post('/patients', [PatientController::class, 'store']);
$router->get('/patients/{id}/edit', [PatientController::class, 'edit']);
$router->post('/patients/{id}/update', [PatientController::class, 'update']);
$router->post('/patients/{id}/delete', [PatientController::class, 'delete']);
$router->post('/patients/{id}/files', [PatientFileController::class, 'store']);
$router->get('/patient-files/{id}', [PatientFileController::class, 'show']);
$router->post('/patient-files/{id}/delete', [PatientFileController::class, 'delete']);
$router->get('/patients/{id}', [PatientController::class, 'show']);

$router->get('/specialties', [SpecialtyController::class, 'index']);
$router->get('/specialties/create', [SpecialtyController::class, 'create']);
$router->post('/specialties', [SpecialtyController::class, 'store']);
$router->get('/specialties/{id}/edit', [SpecialtyController::class, 'edit']);
$router->post('/specialties/{id}/update', [SpecialtyController::class, 'update']);
$router->post('/specialties/{id}/delete', [SpecialtyController::class, 'delete']);

$router->get('/doctors', [DoctorController::class, 'index']);
$router->get('/doctors/create', [DoctorController::class, 'create']);
$router->post('/doctors', [DoctorController::class, 'store']);
$router->get('/doctors/{id}/edit', [DoctorController::class, 'edit']);
$router->post('/doctors/{id}/update', [DoctorController::class, 'update']);
$router->post('/doctors/{id}/delete', [DoctorController::class, 'delete']);

$router->get('/schedules', [ScheduleController::class, 'index']);
$router->get('/schedules/create', [ScheduleController::class, 'create']);
$router->post('/schedules', [ScheduleController::class, 'store']);
$router->get('/schedules/{id}/edit', [ScheduleController::class, 'edit']);
$router->post('/schedules/{id}/update', [ScheduleController::class, 'update']);
$router->post('/schedules/{id}/delete', [ScheduleController::class, 'delete']);

$router->get('/appointments', [AppointmentController::class, 'index']);
$router->get('/appointments/create', [AppointmentController::class, 'create']);
$router->post('/appointments', [AppointmentController::class, 'store']);
$router->get('/appointments/{id}/edit', [AppointmentController::class, 'edit']);
$router->post('/appointments/{id}/update', [AppointmentController::class, 'update']);
$router->post('/appointments/{id}/delete', [AppointmentController::class, 'delete']);
$router->get('/calendar', [AppointmentController::class, 'calendar']);
$router->get('/api/appointments', [AppointmentController::class, 'api']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users', [UserController::class, 'store']);
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
$router->post('/users/{id}/update', [UserController::class, 'update']);
$router->post('/users/{id}/delete', [UserController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
