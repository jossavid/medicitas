<div class="page-actions">

    <div>
        <p class="muted">
            Registro y administración de pacientes.
        </p>
    </div>

    <?php if (
        in_array(
            \App\Core\Auth::role(),
            ['admin', 'receptionist'],
            true
        )
    ): ?>

        <a
            class="btn btn-primary"
            href="<?= url('/patients/create') ?>"
        >
            + Nuevo paciente
        </a>

    <?php endif; ?>

</div>


<div class="panel">

    <div class="table-tools">

        <input
            class="search-input"
            data-table-search="#patientsTable"
            placeholder="Buscar paciente..."
        >

    </div>


    <div class="table-wrap">

        <table id="patientsTable">

            <thead>

                <tr>
                    <th>Paciente</th>
                    <th>Contacto</th>
                    <th>Nacimiento</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

            </thead>


            <tbody>

                <?php foreach ($patients as $p): ?>

                    <tr>

                        <td>

                            <strong>
                                <?= e(
                                    $p['first_name']
                                    . ' '
                                    . $p['last_name']
                                ) ?>
                            </strong>

                            <small class="cell-sub">
                                <?= e(
                                    $p['sex']
                                    ?: 'Sin especificar'
                                ) ?>
                            </small>

                        </td>


                        <td>

                            <?= e(
                                $p['phone']
                                ?: '—'
                            ) ?>

                            <small class="cell-sub">
                                <?= e(
                                    $p['email']
                                    ?: ''
                                ) ?>
                            </small>

                        </td>


                        <td>

                            <?php if ($p['birth_date']): ?>

                                <?= e(
                                    date(
                                        'd/m/Y',
                                        strtotime($p['birth_date'])
                                    )
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <span
                                class="badge <?= $p['active']
                                    ? 'badge-success'
                                    : 'badge-muted' ?>"
                            >
                                <?= $p['active']
                                    ? 'Activo'
                                    : 'Inactivo' ?>
                            </span>

                        </td>


                        <td class="actions">

                            <!--
                            Este botón lo puede ver cualquier
                            usuario autenticado.
                            -->

                            <a
                                class="btn btn-sm"
                                href="<?= url(
                                    '/patients/' . $p['id']
                                ) ?>"
                            >
                                Expediente
                            </a>


                            <?php if (
                                in_array(
                                    \App\Core\Auth::role(),
                                    ['admin', 'receptionist'],
                                    true
                                )
                            ): ?>

                                <a
                                    class="btn btn-sm"
                                    href="<?= url(
                                        '/patients/'
                                        . $p['id']
                                        . '/edit'
                                    ) ?>"
                                >
                                    Editar
                                </a>


                                <form
                                    method="post"
                                    action="<?= url(
                                        '/patients/'
                                        . $p['id']
                                        . '/delete'
                                    ) ?>"
                                    data-confirm="¿Eliminar este paciente?"
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

</div>