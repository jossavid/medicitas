<?php
namespace App\Core;

class Controller
{
    protected function validateCsrf(): void { Csrf::verify(); }

    protected function required(array $fields): array
    {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') $errors[$field] = "$label es obligatorio.";
        }
        return $errors;
    }

    protected function backWithErrors(array $errors, array $old = []): void
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old ?: $_POST;
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('/dashboard')));
        exit;
    }
}
