<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use PDOException;

class DoctorController extends Controller
{
    private function auth(): void { Auth::requireRole(['admin']); }
    public function index(): void { Auth::requireLogin(); view('doctors/index',['title'=>'Médicos','doctors'=>Doctor::all()]); }
    public function create(): void { $this->auth(); view('doctors/form',['title'=>'Nuevo médico','doctor'=>null,'specialties'=>Specialty::active()]); }
    public function store(): void { $this->auth();$this->validateCsrf();$errors=$this->required(['name'=>'Nombre','specialty_id'=>'Especialidad']);if(!empty($_POST['email'])&&!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))$errors['email']='Correo inválido.';if($errors)$this->backWithErrors($errors);Doctor::create($this->payload());flash('success','Médico registrado.');redirect('/doctors'); }
    public function edit($id): void { $this->auth();$doctor=Doctor::find((int)$id);if(!$doctor){http_response_code(404);view('errors/404',['title'=>'Médico no encontrado']);return;}view('doctors/form',['title'=>'Editar médico','doctor'=>$doctor,'specialties'=>Specialty::active()]); }
    public function update($id): void { $this->auth();$this->validateCsrf();$errors=$this->required(['name'=>'Nombre','specialty_id'=>'Especialidad']);if($errors)$this->backWithErrors($errors);Doctor::update((int)$id,$this->payload());flash('success','Médico actualizado.');redirect('/doctors'); }
    public function delete($id): void { $this->auth();$this->validateCsrf();try{Doctor::delete((int)$id);flash('success','Médico eliminado.');}catch(PDOException $e){flash('error','No se puede eliminar porque tiene horarios, citas o usuarios asociados.');}redirect('/doctors'); }
    private function payload(): array {return ['specialty_id'=>(int)($_POST['specialty_id']??0),'name'=>trim($_POST['name']??''),'license_no'=>trim($_POST['license_no']??''),'phone'=>trim($_POST['phone']??''),'email'=>trim($_POST['email']??''),'active'=>isset($_POST['active'])?1:0];}
}
