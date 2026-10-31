<div class="login-card">
    <div class="login-visual">
        <div class="visual-orb orb-one"></div><div class="visual-orb orb-two"></div>
        <div class="medical-symbol">+</div>
        <h2>Organiza la atención médica con claridad.</h2>
        <p>Pacientes, médicos, horarios y citas en una sola aplicación web.</p>
        <div class="login-points"><span>✓ Agenda centralizada</span><span>✓ Control por roles</span><span>✓ Validación de horarios</span></div>
    </div>
    <div class="login-form-panel">
        <div class="login-brand"><span class="brand-mark">+</span><span><strong>Medi</strong>Citas</span></div>
        <span class="eyebrow">Bienvenido</span>
        <h1>Iniciar sesión</h1>
        <p class="muted">Ingresa tus credenciales para acceder al sistema.</p>
        <?php if ($err=error('login')): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
        <form method="post" action="<?= url('/login') ?>" class="stack-form">
            <?= csrf_field() ?>
            <label>Correo electrónico<input type="email" name="email" value="<?= e(old('email')) ?>" placeholder="usuario@medicitas.test" required></label>
            <label>Contraseña<input type="password" name="password" placeholder="••••••••" required></label>
            <button class="btn btn-primary btn-block" type="submit">Ingresar al sistema</button>
        </form>
<!--    <div class="demo-box"><strong>Acceso de demostración</strong><code>admin@medicitas.test</code><code>Admin123!</code></div> -->
    </div>
</div>
