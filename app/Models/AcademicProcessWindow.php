<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicProcessWindow extends Model
{
    use HasFactory, SoftDeletes;

    public const PROCESS_IDEA_PROPOSAL = 'idea_proposal';
    public const PROCESS_IDEA_SELECTION = 'idea_selection';
    public const PROCESS_TEACHER_LOAD_PROJECTION = 'teacher_load_projection';
    public const PROCESS_TEACHER_ASSIGNMENT = 'teacher_assignment';
    public const PROCESS_IDEA_DEMAND_PROJECTION = 'idea_demand_projection';

    protected $fillable = [
        'academic_period_id',
        'process_key',
        'name',
        'start_at',
        'end_at',
        'is_enabled',
        'requires_evaluation',
        'notes',
    ];

    protected $casts = [
        'academic_period_id' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_enabled' => 'boolean',
        'requires_evaluation' => 'boolean',
    ];

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class, 'academic_period_id', 'id');
    }

    public static function processOptions(): array
    {
        return [
            self::PROCESS_IDEA_PROPOSAL => 'Propuesta de ideas',
            self::PROCESS_IDEA_SELECTION => 'Seleccion de ideas',
            self::PROCESS_TEACHER_LOAD_PROJECTION => 'Proyeccion de carga docente',
            self::PROCESS_TEACHER_ASSIGNMENT => 'Asignacion docente',
            self::PROCESS_IDEA_DEMAND_PROJECTION => 'Proyeccion de demanda de ideas',
        ];
    }

    public function getCalculatedStatusAttribute(): string
    {
        if (! $this->is_enabled) {
            return 'Deshabilitada';
        }

        $now = now();

        if (! $this->start_at || ! $this->end_at) {
            return 'Sin fechas';
        }

        if ($now->lt($this->start_at)) {
            return 'Programada';
        }

        if ($now->gt($this->end_at)) {
            return 'Cerrada';
        }

        return 'Activa';
    }

    public function getCalculatedStatusKeyAttribute(): string
    {
        if (! $this->is_enabled) {
            return 'disabled';
        }

        $now = now();

        if (! $this->start_at || ! $this->end_at) {
            return 'no_dates';
        }

        if ($now->lt($this->start_at)) {
            return 'scheduled';
        }

        if ($now->gt($this->end_at)) {
            return 'closed';
        }

        return 'active';
    }
}
