<?php
use App\Core\Auth;
$logged = Auth::check();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
function nav_active(string $needle, string $currentPath): string { return str_contains($currentPath, $needle) ? 'active' : ''; }
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'MediCitas') ?> · MediCitas</title>
<link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
</head>
<body class="<?= $logged ? 'app-body' : 'login-body' ?>">
<?php if ($logged): ?>
<div class="app-shell topnav-layout">
    <header class="app-header">
        <div class="app-header-inner">
            <a class="brand brand-top" href="<?= url('/dashboard') ?>">
                <span class="brand-mark">+</span>
                <span><strong>Medi</strong>Citas<small>Control de citas médicas</small></span>
            </a>

            <button class="menu-btn" id="menuBtn" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>

            <nav class="nav-menu nav-menu-top" id="topNav">
                <a class="<?= nav_active('/dashboard',$currentPath) ?>" href="<?= url('/dashboard') ?>"><span>⌂</span> Dashboard</a>
                <a class="<?= nav_active('/appointments',$currentPath) ?>" href="<?= url('/appointments') ?>"><span>▣</span> Citas</a>
                <a class="<?= nav_active('/calendar',$currentPath) ?>" href="<?= url('/calendar') ?>"><span>▦</span> Calendario</a>
                <a class="<?= nav_active('/patients',$currentPath) ?>" href="<?= url('/patients') ?>"><span>♙</span> Pacientes</a>
                <a class="<?= nav_active('/doctors',$currentPath) ?>" href="<?= url('/doctors') ?>"><span>⚕</span> Médicos</a>
                <a class="<?= nav_active('/specialties',$currentPath) ?>" href="<?= url('/specialties') ?>"><span>✚</span> Especialidades</a>
                <a class="<?= nav_active('/schedules',$currentPath) ?>" href="<?= url('/schedules') ?>"><span>◷</span> Horarios</a>
                <?php if (Auth::role()==='admin'): ?>
                <a class="<?= nav_active('/users',$currentPath) ?>" href="<?= url('/users') ?>"><span>⚙</span> Usuarios</a>
                <?php endif; ?>
            </nav>

            <div class="header-account">
                <div class="sidebar-user header-user">
                    <div class="avatar"><?= e(strtoupper(substr(Auth::user()['name'] ?? 'U',0,1))) ?></div>
                    <div><strong><?= e(Auth::user()['name'] ?? '') ?></strong><small><?= e(['admin'=>'Administrador','doctor'=>'Médico','receptionist'=>'Recepción'][Auth::role()] ?? Auth::role()) ?></small></div>
                </div>
                <form method="post" action="<?= url('/logout') ?>"><?= csrf_field() ?><button class="btn btn-header-logout" type="submit">Salir</button></form>
            </div>
        </div>
    </header>

    <main class="main-content">
        <header class="topbar page-topbar">
            <div><span class="eyebrow">Sistema web</span><h1><?= e($title ?? 'MediCitas') ?></h1></div>
        </header>
        <section class="content-wrap">
            <?php if ($msg=flash('success')): ?><div class="alert alert-success">✓ <?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg=flash('error')): ?><div class="alert alert-error">! <?= e($msg) ?></div><?php endif; ?>
<?php else: ?>
<main class="login-page">
<?php endif; ?>
