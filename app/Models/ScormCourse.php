<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScormCourse extends Model
{
    use HasFactory;

    protected $table = 'scorm_courses';

    protected $fillable = [
        'name',
        'slug',
        'folder_path',
        'launch_file',
    ];

    public function progress()
    {
        return $this->hasMany(ScormUserProgress::class, 'course_id');
    }
}
