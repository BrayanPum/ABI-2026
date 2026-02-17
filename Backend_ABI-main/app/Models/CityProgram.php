<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * city_program table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class CityProgram extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'city_program';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'city_id',
        'program_id',
    ];

    /**
     * Get the city associated with this program offering.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the program associated with this city offering.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get all professors assigned to this city-program combination.
     */
    public function professors()
    {
        return $this->hasMany(Professor::class, 'city_program_id', 'id');
    }

    /**
     * Get all students enrolled in this city-program combination.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'city_program_id', 'id');
    }
}
