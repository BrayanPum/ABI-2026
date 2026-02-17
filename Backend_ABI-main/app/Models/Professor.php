<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPSTORM_META\map;

/**
 * professors table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class Professor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'city_program_id',
        'user_id',
        'committee_leader',
    ];


    /**
     * The attributes that are guarded from mass assignment.
     *
     * @var array<int, string>
     */

    /**
     * Resolve the model class used for the projects relationship.
     */
    protected function getProjectModelClass(): string
    {
        return Project::class;
    }

    /**
     * Get the user associated with the professor.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the city program that the professor belongs to.
     */
    public function cityProgram()
    {
        return $this->belongsTo(CityProgram::class, 'city_program_id', 'id');
    }

    /**
     * Get the projects assigned to the professor.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            $this->getProjectModelClass(),
            'professor_project',
            'professor_id',
            'project_id'
        )->withTimestamps();
    }
}
