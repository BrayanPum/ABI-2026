<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * framework table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class Framework extends Model
{
    use SoftDeletes;
    /**
     * Validation rules for creating a new framework.
     *
     * @var array<string, string>
     */
    static $rules = [
        'name' => 'required|unique:frameworks,name',
        'description' => 'required|min:10',
        'link' => 'nullable|url|max:200',
        'start_year' => 'required|integer|min:1900',
        'end_year' => 'nullable|integer|after_or_equal:start_year',
    ];

    /**
     * Number of records to display per page in pagination.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name','description','link','start_year','end_year'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
    ];

    /**
     * Get validation rules for updating a framework instance.
     *
     * The name uniqueness rule excludes the current framework (by ID).
     *
     * @param int $id The ID of the framework being updated
     * @return array<string, string> The validation rules
     */
    public static function updateRules($id)
    {
        return [
            'name' => 'required|unique:frameworks,name,' . $id,
            'description' => 'required|min:10',
            'link' => 'nullable|url|max:200',
            'start_year' => 'required|integer|min:1900',
            'end_year' => 'nullable|integer|after_or_equal:start_year',
        ];
    }

    /**
     * Get all content frameworks associated with this framework.
     */
    public function contentFrameworks(): HasMany
    {
        return $this->hasMany(ContentFramework::class, 'framework_id', 'id');
    }
}