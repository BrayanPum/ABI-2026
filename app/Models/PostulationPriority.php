<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostulationPriority extends Model
{
    use HasFactory;

    protected $table = 'postulation_priorities';

    protected $primaryKey = 'priority_id';

    protected $fillable = [
        'student_id',
        'postulation_id',
        'priority_order',
    ];

    // El estudiante que asignó la prioridad
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    // La postulación priorizada
    public function postulation()
    {
        return $this->belongsTo(postulation::class, 'postulation_id', 'postulation_id');
    }
}
