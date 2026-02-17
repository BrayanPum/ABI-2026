<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * departments table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class Department extends Model
{
    use HasFactory, SoftDeletes;

    // Specifies the table name associated with this model
    protected $table = 'departments';

    // Defines which attributes can be mass-assigned
    protected $fillable = [
        'name',
    ];

    /**
     * Defines the one-to-many relationship between Department and City.
     * 
     * Each department can have multiple cities associated with it.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'department_id', 'id');
    }
}
