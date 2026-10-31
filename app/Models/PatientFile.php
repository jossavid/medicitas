<?php

namespace App\Models;

use App\Core\Database;

class PatientFile
{
    public static function allByPatient(int $patientId): array
    {
        return Database::fetchAll(
            'SELECT patient_files.*, users.name AS uploaded_by_name
             FROM patient_files
             LEFT JOIN users ON users.id = patient_files.uploaded_by
             WHERE patient_files.patient_id = ?
             ORDER BY patient_files.created_at DESC',
            [$patientId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            'SELECT * FROM patient_files WHERE id = ?',
            [$id]
        );
    }

    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO patient_files
            (
                patient_id,
                category,
                description,
                original_name,
                stored_name,
                mime_type,
                file_size,
                uploaded_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['patient_id'],
                $data['category'],
                $data['description'] ?: null,
                $data['original_name'],
                $data['stored_name'],
                $data['mime_type'],
                $data['file_size'],
                $data['uploaded_by']
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::execute(
            'DELETE FROM patient_files WHERE id = ?',
            [$id]
        );
    }
}