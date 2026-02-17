<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * project table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'evaluation_criteria',
        'thematic_area_id',
        'project_status_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'thematic_area_id' => 'integer',
        'project_status_id' => 'integer',
    ];

    /**
     * Set the title attribute with proper formatting.
     *
     * Formats the title by removing extra whitespace and applying title case.
     * If the value is null, it remains null.
     *
     * @param string|null $value The title value to set
     * @return void
     */
    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = is_null($value)
            ? null
            : Str::of($value)->squish()->title()->toString();
    }

    /**
     * Get the status associated with the project.
     */
    public function projectStatus()
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id', 'id');
    }

    /**
     * Get the thematic area associated with the project.
     */
    public function thematicArea(): BelongsTo
    {
        return $this->belongsTo(ThematicArea::class, 'thematic_area_id', 'id');
    }

    /**
     * Get all versions registered for this project.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Version::class, 'project_id', 'id');
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_project', 'project_id', 'professor_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_project', 'project_id', 'student_id');
    }

    public function contentFrameworkProjects()
    {
        return $this->hasMany(ContentFrameworkProject::class, 'project_id', 'id');
    }

    public function contentFrameworks()
    {
        return $this->belongsToMany(
            ContentFramework::class,
            'content_framework_project',
            'project_id',
            'content_framework_id'
        );
    }

}

