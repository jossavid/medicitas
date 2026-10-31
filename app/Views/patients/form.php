<?php $isEdit=!empty($patient); ?>
<div class="panel form-panel"><form method="post" action="<?= $isEdit?url('/patients/'.$patient['id'].'/update'):url('/patients') ?>" class="form-grid"><?= csrf_field() ?>
<div class="field"><label>Nombres *</label><input name="first_name" value="<?= e(old('first_name',$patient['first_name']??'')) ?>" required><?php if(error('first_name')):?><small class="error-text"><?= e(error('first_name')) ?></small><?php endif;?></div>
<div class="field"><label>Apellidos *</label><input name="last_name" value="<?= e(old('last_name',$patient['last_name']??'')) ?>" required><?php if(error('last_name')):?><small class="error-text"><?= e(error('last_name')) ?></small><?php endif;?></div>
<div class="field"><label>Fecha de nacimiento</label><input type="date" name="birth_date" value="<?= e(old('birth_date',$patient['birth_date']??'')) ?>"></div>
<div class="field"><label>Sexo</label><select name="sex"><option value="">Seleccionar</option><?php foreach(['Femenino','Masculino','Otro'] as $x):?><option <?= old('sex',$patient['sex']??'')===$x?'selected':'' ?>><?= e($x) ?></option><?php endforeach;?></select></div>
<div class="field"><label>Teléfono</label><input name="phone" value="<?= e(old('phone',$patient['phone']??'')) ?>"></div>
<div class="field"><label>Correo</label><input type="email" name="email" value="<?= e(old('email',$patient['email']??'')) ?>"><?php if(error('email')):?><small class="error-text"><?= e(error('email')) ?></small><?php endif;?></div>
<div class="field field-full"><label>Dirección</label><textarea name="address" rows="2"><?= e(old('address',$patient['address']??'')) ?></textarea></div>
<div class="field field-full"><label>Observaciones</label><textarea name="notes" rows="3"><?= e(old('notes',$patient['notes']??'')) ?></textarea></div>
<label class="switch-row field-full"><input type="checkbox" name="active" <?= (int)old('active',$patient['active']??1)===1?'checked':'' ?>><span>Paciente activo</span></label>
<div class="form-actions field-full"><a class="btn" href="<?= url('/patients') ?>">Cancelar</a><button class="btn btn-primary" type="submit"><?= $isEdit?'Guardar cambios':'Registrar paciente' ?></button></div>
</form></div>
