<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\PatientFile;
use PDOException;

class PatientController extends Controller
{
    private function authorizeWrite(): void { Auth::requireRole(['admin','receptionist']); }
    public function index(): void { Auth::requireLogin(); view('patients/index',['title'=>'Pacientes','patients'=>Patient::all()]); }
    public function create(): void { $this->authorizeWrite(); view('patients/form',['title'=>'Nuevo paciente','patient'=>null]); }
    public function store(): void {
        $this->authorizeWrite(); $this->validateCsrf();
        $errors=$this->required(['first_name'=>'Nombres','last_name'=>'Apellidos']);
        if(!empty($_POST['email']) && !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) $errors['email']='El correo no es válido.';
        if($errors) $this->backWithErrors($errors);
        Patient::create($this->payload()); flash('success','Paciente registrado correctamente.'); redirect('/patients');
    }
    public function edit($id): void { $this->authorizeWrite(); $patient=Patient::find((int)$id); if(!$patient){http_response_code(404);view('errors/404',['title'=>'Paciente no encontrado']);return;} view('patients/form',['title'=>'Editar paciente','patient'=>$patient]); }
    public function update($id): void {
        $this->authorizeWrite(); $this->validateCsrf(); $errors=$this->required(['first_name'=>'Nombres','last_name'=>'Apellidos']);
        if(!empty($_POST['email']) && !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) $errors['email']='El correo no es válido.';
        if($errors) $this->backWithErrors($errors); Patient::update((int)$id,$this->payload()); flash('success','Paciente actualizado.'); redirect('/patients');
    }
    public function delete($id): void { $this->authorizeWrite(); $this->validateCsrf(); try{Patient::delete((int)$id);flash('success','Paciente eliminado.');}catch(PDOException $e){flash('error','No se puede eliminar porque tiene citas asociadas. Puedes marcarlo inactivo.');} redirect('/patients'); }
    private function payload(): array { return ['first_name'=>trim($_POST['first_name']??''),'last_name'=>trim($_POST['last_name']??''),'birth_date'=>$_POST['birth_date']??'','sex'=>$_POST['sex']??'','phone'=>trim($_POST['phone']??''),'email'=>trim($_POST['email']??''),'address'=>trim($_POST['address']??''),'notes'=>trim($_POST['notes']??''),'active'=>isset($_POST['active'])?1:0]; }
    public function show($id): void
    {
        Auth::requireLogin();

        $patientId = (int) $id;

        $patient = Patient::find($patientId);

        if (!$patient) {
            http_response_code(404);
            view('errors/404', [
                'title' => 'Paciente no encontrado'
            ]);
            return;
        }

        $appointments = Appointment::byPatient($patientId);

        $files = PatientFile::allByPatient($patientId);

        view('patients/show', [
            'title' => 'Expediente del paciente',
            'patient' => $patient,
            'appointments' => $appointments,
            'files' => $files,
        ]);
    }
}
