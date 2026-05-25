<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormatoRegistro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'formato_registros';

    protected $fillable = [
        'formato_tipo_id',
        'user_id',
    ];

    public function formatoTipo()
    {
        return $this->belongsTo(FormatoTipo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function valores()
    {
        return $this->hasMany(FormatoValor::class);
    }
}
