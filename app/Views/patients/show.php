<?php
use App\Core\Auth;

$statusLabels = [
    'pending' => 'Pendiente',
    'confirmed' => 'Confirmada',
    'completed' => 'Atendida',
    'cancelled' => 'Cancelada',
    'no_show' => 'No asistió',
];
?>

<div class="page-actions">
    <div>
        <p class="muted">
            Información general, historial de citas y archivos médicos.
        </p>
    </div>

    <div class="actions">
        <a class="btn" href="<?= url('/patients') ?>">
            Volver
        </a>

        <?php if (in_array(Auth::role(), ['admin', 'receptionist'], true)): ?>
            <a
                class="btn btn-primary"
                href="<?= url('/patients/' . $patient['id'] . '/edit') ?>"
            >
                Editar paciente
            </a>
        <?php endif; ?>
    </div>
</div>


<!-- DATOS DEL PACIENTE -->
<div class="panel">
    <div class="panel-head">
        <div>
            <h3>
                <?= e($patient['first_name'] . ' ' . $patient['last_name']) ?>
            </h3>

            <span class="badge <?= $patient['active'] ? 'badge-success' : 'badge-muted' ?>">
                <?= $patient['active'] ? 'Paciente activo' : 'Paciente inactivo' ?>
            </span>
        </div>
    </div>

    <div class="form-grid">

        <div class="field">
            <label>Fecha de nacimiento</label>
            <div>
                <?= $patient['birth_date']
                    ? e(date('d/m/Y', strtotime($patient['birth_date'])))
                    : 'No registrada' ?>
            </div>
        </div>

        <div class="field">
            <label>Sexo</label>
            <div><?= e($patient['sex'] ?: 'No especificado') ?></div>
        </div>

        <div class="field">
            <label>Teléfono</label>
            <div><?= e($patient['phone'] ?: 'No registrado') ?></div>
        </div>

        <div class="field">
            <label>Correo electrónico</label>
            <div><?= e($patient['email'] ?: 'No registrado') ?></div>
        </div>

        <div class="field field-full">
            <label>Dirección</label>
            <div><?= e($patient['address'] ?: 'No registrada') ?></div>
        </div>

        <div class="field field-full">
            <label>Observaciones</label>
            <div>
                <?= nl2br(e($patient['notes'] ?: 'Sin observaciones')) ?>
            </div>
        </div>

    </div>
</div>


<br>


<!-- HISTORIAL DE CITAS -->
<div class="panel">

    <div class="panel-head">
        <div>
            <h3>Historial de citas</h3>
            <p class="muted">
                Consultas registradas para este paciente.
            </p>
        </div>

        <?php if (in_array(Auth::role(), ['admin', 'receptionist'], true)): ?>
            <a
                class="btn btn-primary"
                href="<?= url('/appointments/create') ?>"
            >
                + Nueva cita
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($appointments)): ?>

        <div class="empty-state">
            Este paciente todavía no tiene citas registradas.
        </div>

    <?php else: ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($appointments as $appointment): ?>

                        <?php
                        $status = $appointment['status'];

                        $statusText =
                            $statusLabels[$status]
                            ?? $status;
                        ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= e(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $appointment['appointment_date']
                                            )
                                        )
                                    ) ?>
                                </strong>

                                <small class="cell-sub">
                                    <?= e(
                                        substr(
                                            $appointment['appointment_time'],
                                            0,
                                            5
                                        )
                                    ) ?>
                                </small>
                            </td>

                            <td>
                                <?= e($appointment['doctor_name']) ?>
                            </td>

                            <td>
                                <?= e($appointment['specialty_name']) ?>
                            </td>

                            <td>
                                <?= e($appointment['reason']) ?>
                            </td>

                            <td>
                                <span class="badge status-<?= e($status) ?>">
                                    <?= e($statusText) ?>
                                </span>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>


<br>


<!-- ARCHIVOS MÉDICOS -->
<div class="panel">

    <div class="panel-head">
        <div>
            <h3>Archivos médicos</h3>

            <p class="muted">
                Radiografías, tomografías, resultados y otros documentos.
            </p>
        </div>
    </div>


    <?php if (in_array(Auth::role(), ['admin', 'receptionist'], true)): ?>

        <form
            method="post"
            action="<?= url('/patients/' . $patient['id'] . '/files') ?>"
            enctype="multipart/form-data"
            class="form-grid"
        >

            <?= csrf_field() ?>

            <div class="field">

                <label>Tipo de archivo *</label>

                <select name="category" required>

                    <option value="">
                        Seleccionar
                    </option>

                    <?php
                    $categories = [
                        'Radiografía',
                        'Tomografía',
                        'Ultrasonido',
                        'Laboratorio',
                        'Receta',
                        'Informe médico',
                        'Otro'
                    ];
                    ?>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= e($category) ?>"
                            <?= old('category') === $category
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($category) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <?php if (error('category')): ?>
                    <small class="error-text">
                        <?= e(error('category')) ?>
                    </small>
                <?php endif; ?>

            </div>


            <div class="field">

                <label>Archivo *</label>

                <input
                    type="file"
                    name="medical_file"
                    accept=".jpg,.jpeg,.png,.pdf"
                    required
                >

                <?php if (error('medical_file')): ?>
                    <small class="error-text">
                        <?= e(error('medical_file')) ?>
                    </small>
                <?php endif; ?>

                <small class="muted">
                    JPG, PNG o PDF. Máximo 8 MB.
                </small>

            </div>


            <div class="field field-full">

                <label>Descripción</label>

                <textarea
                    name="description"
                    rows="2"
                    placeholder="Ejemplo: Radiografía de tórax realizada en octubre"
                ><?= e(old('description')) ?></textarea>

            </div>


            <div class="form-actions field-full">

                <button
                    class="btn btn-primary"
                    type="submit"
                >
                    Subir archivo
                </button>

            </div>

        </form>

    <?php endif; ?>


    <br>


    <?php if (empty($files)): ?>

        <div class="empty-state">
            No hay archivos médicos registrados para este paciente.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Archivo</th>
                        <th>Descripción</th>
                        <th>Tamaño</th>
                        <th>Subido por</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($files as $file): ?>

                        <?php

                        $size = (int) $file['file_size'];

                        if ($size >= 1024 * 1024) {
                            $formattedSize =
                                number_format(
                                    $size / (1024 * 1024),
                                    1
                                ) . ' MB';
                        } else {
                            $formattedSize =
                                number_format(
                                    $size / 1024,
                                    1
                                ) . ' KB';
                        }

                        ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= e($file['category']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= e($file['original_name']) ?>
                            </td>

                            <td>
                                <?= e(
                                    $file['description']
                                    ?: 'Sin descripción'
                                ) ?>
                            </td>

                            <td>
                                <?= e($formattedSize) ?>
                            </td>

                            <td>
                                <?= e(
                                    $file['uploaded_by_name']
                                    ?: 'Usuario eliminado'
                                ) ?>
                            </td>

                            <td>
                                <?= e(
                                    date(
                                        'd/m/Y H:i',
                                        strtotime($file['created_at'])
                                    )
                                ) ?>
                            </td>

                            <td class="actions">

                                <a
                                    class="btn btn-sm"
                                    href="<?= url(
                                        '/patient-files/' . $file['id']
                                    ) ?>"
                                    target="_blank"
                                >
                                    Ver
                                </a>

                                <?php if (
                                    in_array(
                                        Auth::role(),
                                        ['admin', 'receptionist'],
                                        true
                                    )
                                ): ?>

                                    <form
                                        method="post"
                                        action="<?= url(
                                            '/patient-files/' .
                                            $file['id'] .
                                            '/delete'
                                        ) ?>"
                                        data-confirm="¿Eliminar este archivo médico?"
                                    >

                                        <?= csrf_field() ?>

                                        <button
                                            class="btn btn-sm btn-danger"
                                            type="submit"
                                        >
                                            Eliminar
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>