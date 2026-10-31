<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Specialty;
use PDOException;

class SpecialtyController extends Controller
{
    private function auth(): void { Auth::requireRole(['admin']); }
    public function index(): void { Auth::requireLogin(); view('specialties/index',['title'=>'Especialidades','specialties'=>Specialty::all()]); }
    public function create(): void { $this->auth(); view('specialties/form',['title'=>'Nueva especialidad','specialty'=>null]); }
    public function store(): void { $this->auth();$this->validateCsrf();$errors=$this->required(['name'=>'Nombre']);if($errors)$this->backWithErrors($errors);try{Specialty::create($this->payload());flash('success','Especialidad creada.');}catch(PDOException $e){flash('error','Ya existe una especialidad con ese nombre.');}redirect('/specialties'); }
    public function edit($id): void { $this->auth();$specialty=Specialty::find((int)$id);if(!$specialty){http_response_code(404);view('errors/404',['title'=>'Especialidad no encontrada']);return;}view('specialties/form',['title'=>'Editar especialidad','specialty'=>$specialty]); }
    public function update($id): void { $this->auth();$this->validateCsrf();$errors=$this->required(['name'=>'Nombre']);if($errors)$this->backWithErrors($errors);try{Specialty::update((int)$id,$this->payload());flash('success','Especialidad actualizada.');}catch(PDOException $e){flash('error','No se pudo actualizar; revisa que el nombre no esté repetido.');}redirect('/specialties'); }
    public function delete($id): void { $this->auth();$this->validateCsrf();try{Specialty::delete((int)$id);flash('success','Especialidad eliminada.');}catch(PDOException $e){flash('error','No se puede eliminar porque tiene médicos asociados.');}redirect('/specialties'); }
    private function payload(): array {return ['name'=>trim($_POST['name']??''),'description'=>trim($_POST['description']??''),'active'=>isset($_POST['active'])?1:0];}
}
