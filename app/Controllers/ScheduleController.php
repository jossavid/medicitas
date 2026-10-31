<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Doctor;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    private function auth(): void { Auth::requireRole(['admin']); }
    public function index(): void { Auth::requireLogin(); view('schedules/index',['title'=>'Horarios','schedules'=>Schedule::all()]); }
    public function create(): void { $this->auth(); view('schedules/form',['title'=>'Nuevo horario','schedule'=>null,'doctors'=>Doctor::active()]); }
    public function store(): void { $this->auth();$this->validateCsrf();$errors=$this->validateInput();if($errors)$this->backWithErrors($errors);Schedule::create($this->payload());flash('success','Horario creado.');redirect('/schedules'); }
    public function edit($id): void { $this->auth();$schedule=Schedule::find((int)$id);if(!$schedule){http_response_code(404);view('errors/404',['title'=>'Horario no encontrado']);return;}view('schedules/form',['title'=>'Editar horario','schedule'=>$schedule,'doctors'=>Doctor::active()]); }
    public function update($id): void { $this->auth();$this->validateCsrf();$errors=$this->validateInput();if($errors)$this->backWithErrors($errors);Schedule::update((int)$id,$this->payload());flash('success','Horario actualizado.');redirect('/schedules'); }
    public function delete($id): void { $this->auth();$this->validateCsrf();Schedule::delete((int)$id);flash('success','Horario eliminado.');redirect('/schedules'); }
    private function validateInput(): array { $errors=$this->required(['doctor_id'=>'Médico','weekday'=>'Día','start_time'=>'Hora inicial','end_time'=>'Hora final','slot_minutes'=>'Duración']);if(($_POST['start_time']??'')>=($_POST['end_time']??''))$errors['end_time']='La hora final debe ser posterior a la inicial.';if((int)($_POST['slot_minutes']??0)<10)$errors['slot_minutes']='La duración mínima es de 10 minutos.';return $errors; }
    private function payload(): array { return ['doctor_id'=>(int)($_POST['doctor_id']??0),'weekday'=>(int)($_POST['weekday']??0),'start_time'=>$_POST['start_time']??'','end_time'=>$_POST['end_time']??'','slot_minutes'=>(int)($_POST['slot_minutes']??30),'active'=>isset($_POST['active'])?1:0]; }
}
