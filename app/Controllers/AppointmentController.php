<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Schedule;
use PDOException;

class AppointmentController extends Controller
{
    private function doctorScope(): ?int { return Auth::role()==='doctor' ? (int)(Auth::user()['doctor_id']??0) : null; }
    private function authorizeWrite(): void { Auth::requireRole(['admin','receptionist','doctor']); }
    public function index(): void { Auth::requireLogin();view('appointments/index',['title'=>'Citas','appointments'=>Appointment::all($this->doctorScope())]); }
    public function create(): void { Auth::requireRole(['admin','receptionist']);view('appointments/form',['title'=>'Nueva cita','appointment'=>null,'patients'=>Patient::active(),'doctors'=>Doctor::active()]); }
    public function store(): void {
        Auth::requireRole(['admin','receptionist']);$this->validateCsrf();$errors=$this->validateInput();if($errors)$this->backWithErrors($errors);
        $d=$this->payload();$d['created_by']=Auth::id();
        try{Appointment::create($d);flash('success','Cita registrada correctamente.');redirect('/appointments');}catch(PDOException $e){$this->backWithErrors(['appointment_time'=>'Ya existe una cita para ese médico en la fecha y hora seleccionadas.']);}
    }
    public function edit($id): void {
        $this->authorizeWrite();$appointment=Appointment::find((int)$id);if(!$appointment){http_response_code(404);view('errors/404',['title'=>'Cita no encontrada']);return;}
        if(Auth::role()==='doctor' && (int)$appointment['doctor_id']!==$this->doctorScope()){http_response_code(403);view('errors/403',['title'=>'Acceso denegado']);return;}
        view('appointments/form',['title'=>'Editar cita','appointment'=>$appointment,'patients'=>Patient::active(),'doctors'=>Doctor::active()]);
    }
    public function update($id): void {
        $this->authorizeWrite();$this->validateCsrf();$appointment=Appointment::find((int)$id);if(!$appointment){redirect('/appointments');}
        if(Auth::role()==='doctor'){
            if((int)$appointment['doctor_id']!==$this->doctorScope()){http_response_code(403);view('errors/403',['title'=>'Acceso denegado']);return;}
            $d=$appointment;$d['status']=$_POST['status']??$appointment['status'];$d['notes']=trim($_POST['notes']??'');Appointment::update((int)$id,$d);flash('success','Estado de cita actualizado.');redirect('/appointments');
        }
        $errors=$this->validateInput((int)$id);if($errors)$this->backWithErrors($errors);Appointment::update((int)$id,$this->payload());flash('success','Cita actualizada.');redirect('/appointments');
    }
    public function delete($id): void { Auth::requireRole(['admin','receptionist']);$this->validateCsrf();Appointment::delete((int)$id);flash('success','Cita eliminada.');redirect('/appointments'); }
    public function calendar(): void { Auth::requireLogin();view('appointments/calendar',['title'=>'Calendario de citas','appointments'=>Appointment::all($this->doctorScope())]); }
    public function api(): void { Auth::requireLogin();header('Content-Type: application/json; charset=utf-8');echo json_encode(Appointment::all($this->doctorScope()),JSON_UNESCAPED_UNICODE); }
    private function validateInput(?int $ignoreId=null): array {
        $errors=$this->required(['patient_id'=>'Paciente','doctor_id'=>'Médico','appointment_date'=>'Fecha','appointment_time'=>'Hora','reason'=>'Motivo']);
        $doctor=(int)($_POST['doctor_id']??0);$date=$_POST['appointment_date']??'';$time=$_POST['appointment_time']??'';$duration=(int)($_POST['duration_minutes']??30);
        if($date && $date < date('Y-m-d')) $errors['appointment_date']='No se pueden crear citas en fechas pasadas.';
        if($doctor && $date && $time){
            if(Appointment::hasConflict($doctor,$date,$time,$ignoreId))$errors['appointment_time']='Ese médico ya tiene una cita a esa hora.';
            if(!Schedule::matches($doctor,$date,$time,$duration))$errors['appointment_time']='La fecha y hora seleccionadas están fuera del horario configurado para el médico.';
        }
        return $errors;
    }
    private function payload(): array { return ['patient_id'=>(int)($_POST['patient_id']??0),'doctor_id'=>(int)($_POST['doctor_id']??0),'appointment_date'=>$_POST['appointment_date']??'','appointment_time'=>$_POST['appointment_time']??'','duration_minutes'=>(int)($_POST['duration_minutes']??30),'reason'=>trim($_POST['reason']??''),'status'=>$_POST['status']??'pending','notes'=>trim($_POST['notes']??'')]; }
}
