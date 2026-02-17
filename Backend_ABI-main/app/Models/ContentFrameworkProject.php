<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * content_framework_project table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class ContentFrameworkProject extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'content_framework_project';
    
    /**
     * Validation rules for creating a new record.
     *
     * Both foreign keys are required.
     *
     * @var array<string, string>
     */
    static $rules = [
		'content_framework_id' => 'required',
		'project_id' => 'required',
    ];

    /**
     * Number of records to display per page in pagination.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['content_framework_id','project_id'];


    /**
     * Get the content framework associated with this pivot record.
     */
    public function contentFramework()
    {
        return $this->belongsTo(ContentFramework::class, 'content_framework_id', 'id');
    }

    /**
     * Get the project associated with this pivot record.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
