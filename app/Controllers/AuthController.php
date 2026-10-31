<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) redirect('/dashboard');
        view('auth/login', ['title' => 'Iniciar sesión']);
    }

    public function login(): void
    {
        $this->validateCsrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$email || !$password) $this->backWithErrors(['login' => 'Ingresa correo y contraseña.']);
        if (!Auth::attempt($email, $password)) $this->backWithErrors(['login' => 'Credenciales incorrectas o usuario inactivo.'], ['email' => $email]);
        flash('success', 'Bienvenido a MediCitas.');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->validateCsrf();
        Auth::logout();
        session_start();
        flash('success', 'Sesión cerrada correctamente.');
        redirect('/');
    }
}
