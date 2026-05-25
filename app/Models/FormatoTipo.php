<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatoTipo extends Model
{
    use HasFactory;

    protected $table = 'formato_tipos';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'icono',
        'color',
        'roles_acceso',
        'activo',
    ];

    protected $casts = [
        'roles_acceso' => 'array',
        'activo' => 'boolean',
    ];

    public function campos()
    {
        return $this->hasMany(FormatoCampo::class)->orderBy('orden');
    }

    public function registros()
    {
        return $this->hasMany(FormatoRegistro::class);
    }

    public function esAccesiblePor(string $role): bool
    {
        return in_array($role, $this->roles_acceso ?? []);
    }
}
