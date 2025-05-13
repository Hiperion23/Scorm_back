<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScormTracking extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'attempt',
        'element',
        'value',
    ];
}
