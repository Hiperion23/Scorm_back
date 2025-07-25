<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternativa extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotspot_id',
        'opcion',
        'ancho',
        'alto',
        'eje_x',
        'eje_y',
        'radio',
        'img_rpta',
        'rpta_ancho',
        'rpta_alto',
        'rpta_eje_x',
        'rpta_eje_y',
    ];

    public function hotspot()
    {
        return $this->belongsTo(Hotspot::class);
    }
}
