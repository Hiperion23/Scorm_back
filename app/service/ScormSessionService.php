<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class ScormSessionService
{
    protected string $key = 'scorm_data';

    public function initialize()
    {
        $default = [
            "cmi.core.student_name" => "Ramírez, Hamilton",
            "cmi.core.student_id" => "hamilton@daktico.com",
            "cmi.core.lesson_status" => "not attempted",
            "cmi.core.score.raw" => "0",
            "cmi.suspend_data" => ""
        ];

        Session::put($this->key, $default);
    }

    public function get(string $element): string
    {
        $data = Session::get($this->key, []);
        return $data[$element] ?? '';
    }

    public function set(string $element, string $value): void
    {
        $data = Session::get($this->key, []);
        $data[$element] = $value;
        Session::put($this->key, $data);
    }

    public function commit(): void
    {
        // Aquí podrías persistir datos en la base de datos si deseas
    }

    public function finish(): void
    {
        Session::forget($this->key);
    }

    public function all(): array
    {
        return Session::get($this->key, []);
    }
}
