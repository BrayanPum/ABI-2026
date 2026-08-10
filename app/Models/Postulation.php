<?php

namespace App\Models;

use App\Models\Professor\ProfessorProject;
use Illuminate\Database\Eloquent\Model;

class Postulation extends Model
{
    protected $table = 'postulations';

    protected $primaryKey = 'postulation_id';

    protected $fillable = [
        'project_id',
        'lead_student_id',
        'justification',
        'modality',
        'accepted_terms',
        'grades_file',
        'status',
        'review_comment',
    ];

    protected $casts = [
        'accepted_terms' => 'boolean',
    ];

    // La idea a la que se postula
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    // El estudiante que creó la postulación (líder)
    public function leadStudent()
    {
        return $this->belongsTo(Student::class, 'lead_student_id', 'id');
    }

    // Todos los integrantes del equipo
    public function members()
    {
        return $this->hasMany(PostulationMember::class, 'postulation_id', 'postulation_id');
    }

    // Solo los estudiantes (a través de members)
    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'postulation_members',
            'postulation_id',
            'student_id'
        )->withPivot('role_description', 'is_lead');
    }

    // Las prioridades asignadas a esta postulación
    public function priorities()
    {
        return $this->hasMany(PostulationPriority::class, 'postulation_id', 'postulation_id');
    }

    // El profesor que revisa (vía professor_project)
    public function reviewer()
    {
        return $this->hasOneThrough(
            Professor::class,
            ProfessorProject::class,
            'project_id',   // FK en professor_project
            'id',           // PK en professors
            'project_id',   // FK local en applications
            'professor_id'  // FK en professor_project
        );
    }

    // Scopes útiles
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
