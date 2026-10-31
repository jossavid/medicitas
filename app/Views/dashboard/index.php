<div class="hero-strip">
    <div><span class="eyebrow">Resumen general</span><h2>Hola, <?= e(explode(' ', \App\Core\Auth::user()['name'])[0]) ?> 👋</h2><p>Consulta rápidamente el estado de la agenda médica.</p></div>
    <?php if (\App\Core\Auth::role() !== 'doctor'): ?><a class="btn btn-primary" href="<?= url('/appointments/create') ?>">+ Nueva cita</a><?php endif; ?>
</div>
<div class="stats-grid">
    <article class="stat-card"><span class="stat-icon">▣</span><div><small>Citas de hoy</small><strong><?= $stats['today'] ?></strong></div></article>
    <article class="stat-card"><span class="stat-icon">◷</span><div><small>Pendientes / confirmadas</small><strong><?= $stats['pending'] ?></strong></div></article>
    <article class="stat-card"><span class="stat-icon">♙</span><div><small>Pacientes activos</small><strong><?= $stats['patients'] ?></strong></div></article>
    <article class="stat-card"><span class="stat-icon">⚕</span><div><small>Médicos activos</small><strong><?= $stats['doctors'] ?></strong></div></article>
</div>
<div class="panel">
    <div class="panel-head"><div><span class="eyebrow">Agenda</span><h3>Próximas citas</h3></div><a class="text-link" href="<?= url('/appointments') ?>">Ver todas →</a></div>
    <?php if (!$upcoming): ?><div class="empty-state">No hay citas próximas registradas.</div><?php else: ?>
    <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Médico</th><th>Estado</th></tr></thead><tbody>
    <?php foreach($upcoming as $a): ?><tr><td><?= e(date('d/m/Y',strtotime($a['appointment_date']))) ?></td><td><?= e(substr($a['appointment_time'],0,5)) ?></td><td><strong><?= e($a['patient_name']) ?></strong></td><td><?= e($a['doctor_name']) ?></td><td><span class="badge status-<?= e($a['status']) ?>"><?= e(['pending'=>'Pendiente','confirmed'=>'Confirmada','completed'=>'Atendida','cancelled'=>'Cancelada','no_show'=>'No asistió'][$a['status']] ?? $a['status']) ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?>
</div>
