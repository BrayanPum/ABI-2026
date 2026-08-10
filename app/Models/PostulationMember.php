<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulationMember extends Model
{
    protected $table = 'postulation_members';

    protected $primaryKey = 'member_id';

    protected $fillable = [
        'postulation_id',
        'student_id',
        'role_description',
        'is_lead',
    ];

    protected $casts = [
        'is_lead' => 'boolean',
    ];

    // La postulación a la que pertenece
    public function application()
    {
        return $this->belongsTo(Postulation::class, 'postulation_id', 'postulation_id');
    }

    // El estudiante integrante
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
