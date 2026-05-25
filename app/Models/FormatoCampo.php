<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatoCampo extends Model
{
    use HasFactory;

    protected $table = 'formato_campos';

    protected $fillable = [
        'formato_tipo_id',
        'nombre',
        'etiqueta',
        'tipo',
        'opciones',
        'requerido',
        'seccion',
        'orden',
    ];

    protected $casts = [
        'opciones' => 'array',
        'requerido' => 'boolean',
    ];

    public function formatoTipo()
    {
        return $this->belongsTo(FormatoTipo::class);
    }
}
