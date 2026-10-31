<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;
use App\Models\Doctor;
use PDOException;

class UserController extends Controller
{
    private function auth(): void { Auth::requireRole(['admin']); }
    public function index(): void { $this->auth();view('users/index',['title'=>'Usuarios','users'=>User::all()]); }
    public function create(): void { $this->auth();view('users/form',['title'=>'Nuevo usuario','account'=>null,'doctors'=>Doctor::active()]); }
    public function store(): void { $this->auth();$this->validateCsrf();$errors=$this->validateInput();if($errors)$this->backWithErrors($errors);User::create($this->payload());flash('success','Usuario creado.');redirect('/users'); }
    public function edit($id): void { $this->auth();$account=User::find((int)$id);if(!$account){http_response_code(404);view('errors/404',['title'=>'Usuario no encontrado']);return;}view('users/form',['title'=>'Editar usuario','account'=>$account,'doctors'=>Doctor::active()]); }
    public function update($id): void { $this->auth();$this->validateCsrf();$errors=$this->validateInput((int)$id,false);if($errors)$this->backWithErrors($errors);User::update((int)$id,$this->payload());if(Auth::id()===(int)$id){$_SESSION['user']=array_merge($_SESSION['user'],array_intersect_key($this->payload(),array_flip(['name','email','role','doctor_id','active'])));}flash('success','Usuario actualizado.');redirect('/users'); }
    public function delete($id): void { $this->auth();$this->validateCsrf();if(Auth::id()===(int)$id){flash('error','No puedes eliminar tu propio usuario mientras tienes sesión iniciada.');redirect('/users');}try{User::delete((int)$id);flash('success','Usuario eliminado.');}catch(PDOException $e){flash('error','No se puede eliminar porque el usuario creó citas. Puedes desactivarlo.');}redirect('/users'); }
    private function validateInput(?int $id=null,bool $passwordRequired=true): array { $fields=['name'=>'Nombre','email'=>'Correo','role'=>'Rol'];if($passwordRequired)$fields['password']='Contraseña';$errors=$this->required($fields);$email=trim($_POST['email']??'');if($email&&!filter_var($email,FILTER_VALIDATE_EMAIL))$errors['email']='Correo inválido.';elseif($email&&User::emailExists($email,$id))$errors['email']='Este correo ya está registrado.';if(($_POST['role']??'')==='doctor'&&empty($_POST['doctor_id']))$errors['doctor_id']='Selecciona el médico asociado.';if(!empty($_POST['password'])&&strlen($_POST['password'])<8)$errors['password']='La contraseña debe tener al menos 8 caracteres.';return $errors; }
    private function payload(): array { return ['name'=>trim($_POST['name']??''),'email'=>trim($_POST['email']??''),'password'=>$_POST['password']??'','role'=>$_POST['role']??'receptionist','doctor_id'=>($_POST['role']??'')==='doctor'?(int)($_POST['doctor_id']??0):null,'active'=>isset($_POST['active'])?1:0]; }
}
