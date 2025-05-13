<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScormCoursesTable extends Migration
{
    public function up(): void
    {
        Schema::create('scorm_courses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del curso
            $table->string('slug')->unique(); // Para URLs limpias
            $table->string('launch_file'); // index.html u otro
            $table->string('folder_path'); // Carpeta local (ej: scorm/curso-abc-123)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scorm_courses');
    }
}
