<div class="page-actions"><p class="muted">Vista mensual de las citas registradas.</p><?php if(in_array(\App\Core\Auth::role(),['admin','receptionist'],true)):?><a class="btn btn-primary" href="<?= url('/appointments/create') ?>">+ Nueva cita</a><?php endif;?></div>
<div class="panel calendar-panel" id="calendarApp" data-events='<?= e(json_encode(array_map(fn($a)=>['id'=>$a['id'],'date'=>$a['appointment_date'],'time'=>substr($a['appointment_time'],0,5),'patient'=>$a['patient_name'],'doctor'=>$a['doctor_name'],'status'=>$a['status']],$appointments),JSON_UNESCAPED_UNICODE)) ?>'>
<div class="calendar-head"><button class="btn btn-sm" id="prevMonth">←</button><h3 id="calendarTitle"></h3><button class="btn btn-sm" id="nextMonth">→</button></div>
<div class="calendar-weekdays"><span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span></div>
<div class="calendar-grid" id="calendarGrid"></div>
</div>
