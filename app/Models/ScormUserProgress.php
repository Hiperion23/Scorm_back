<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScormUserProgress extends Model
{
    use HasFactory;

    protected $table = 'scorm_user_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'student_id',
        'student_name',
        'lesson_status',
        'score_raw',
        'suspend_data',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el curso (opcional, si tienes modelo de curso)
    public function course()
    {
        return $this->belongsTo(ScormCourse::class, 'course_id');
    }
}
