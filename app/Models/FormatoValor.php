<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatoValor extends Model
{
    use HasFactory;

    protected $table = 'formato_valores';

    protected $fillable = [
        'formato_registro_id',
        'formato_campo_id',
        'valor',
    ];

    public function registro()
    {
        return $this->belongsTo(FormatoRegistro::class);
    }

    public function campo()
    {
        return $this->belongsTo(FormatoCampo::class);
    }
}
