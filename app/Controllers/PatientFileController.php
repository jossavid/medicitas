<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Patient;
use App\Models\PatientFile;
use RuntimeException;
use Throwable;

class PatientFileController extends Controller
{
    private const MAX_FILE_SIZE = 8 * 1024 * 1024; // 8 MB

    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];

    private function authorizeWrite(): void
    {
        Auth::requireRole(['admin', 'receptionist']);
    }

    public function store($patientId): void
    {
        $this->authorizeWrite();
        $this->validateCsrf();

        $patientId = (int) $patientId;
        $patient = Patient::find($patientId);

        if (!$patient) {
            http_response_code(404);
            view('errors/404', ['title' => 'Paciente no encontrado']);
            return;
        }

        $errors = $this->required([
            'category' => 'Tipo de archivo'
        ]);

        if (
            !isset($_FILES['medical_file']) ||
            $_FILES['medical_file']['error'] !== UPLOAD_ERR_OK
        ) {
            $errors['medical_file'] = 'Debes seleccionar un archivo válido.';
        }

        if ($errors) {
            $this->backWithErrors($errors);
        }

        $file = $_FILES['medical_file'];

        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->backWithErrors([
                'medical_file' => 'El archivo no puede superar los 8 MB.'
            ]);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            $this->backWithErrors([
                'medical_file' => 'Solo se permiten archivos JPG, PNG o PDF.'
            ]);
        }

        $extension = self::ALLOWED_TYPES[$mimeType];

        $storageDirectory = BASE_PATH . '/storage/patient_files';

        if (!is_dir($storageDirectory)) {
            mkdir($storageDirectory, 0775, true);
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;

        $destination = $storageDirectory . '/' . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('No fue posible guardar el archivo.');
        }

        try {
            PatientFile::create([
                'patient_id' => $patientId,
                'category' => trim($_POST['category'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'original_name' => basename($file['name']),
                'stored_name' => $storedName,
                'mime_type' => $mimeType,
                'file_size' => (int) $file['size'],
                'uploaded_by' => Auth::id(),
            ]);
        } catch (Throwable $e) {
            if (is_file($destination)) {
                unlink($destination);
            }

            throw $e;
        }

        flash('success', 'Archivo médico agregado correctamente.');
        redirect('/patients/' . $patientId);
    }

    public function show($id): void
    {
        Auth::requireLogin();

        $file = PatientFile::find((int) $id);

        if (!$file) {
            http_response_code(404);
            exit('Archivo no encontrado.');
        }

        $path = BASE_PATH . '/storage/patient_files/' . $file['stored_name'];

        if (!is_file($path)) {
            http_response_code(404);
            exit('El archivo físico no existe.');
        }

        $originalName = str_replace(
            ["\r", "\n", '"'],
            '',
            basename($file['original_name'])
        );

        header('Content-Type: ' . $file['mime_type']);
        header('Content-Length: ' . filesize($path));
        header(
            'Content-Disposition: inline; filename="' . $originalName . '"'
        );

        readfile($path);
        exit;
    }

    public function delete($id): void
    {
        $this->authorizeWrite();
        $this->validateCsrf();

        $file = PatientFile::find((int) $id);

        if (!$file) {
            flash('error', 'El archivo no existe.');
            redirect('/patients');
        }

        $patientId = (int) $file['patient_id'];

        $path = BASE_PATH . '/storage/patient_files/' . $file['stored_name'];

        PatientFile::delete((int) $id);

        if (is_file($path)) {
            unlink($path);
        }

        flash('success', 'Archivo médico eliminado.');
        redirect('/patients/' . $patientId);
    }
}