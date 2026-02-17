<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * students table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_id',
        'name',
        'last_name',
        'phone',
        'semester',
        'city_program_id',
        'user_id',
    ];

    /**
     * Resolve the model class used for the projects relationship.
     */
    protected function getProjectModelClass(): string
    {
        return Project::class;
    }

    /**
     * Get the user associated with the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the city program that the student belongs to.
     */
    public function cityProgram()
    {
        return $this->belongsTo(CityProgram::class, 'city_program_id', 'id');
    }

    /**
     * Get the projects assigned to the student.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            $this->getProjectModelClass(),
            'student_project',
            'student_id',
            'project_id'
        )->withTimestamps();
    }
}
