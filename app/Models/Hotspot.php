<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    use HasFactory;

    protected $fillable = [
        'intro_video',
        'tiempo',
        'pausar_en',
        'habilidad',
        'id_modulo',
    ];

    public function alternativas()
    {
        return $this->hasMany(Alternativa::class);
    }
}
