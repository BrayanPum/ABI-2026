<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * version table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class Version extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_id' => 'integer',
    ];

    /**
     * Parent project of the version.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Direct relationship with the completed content.
     */
    public function contentVersions(): HasMany
    {
        return $this->hasMany(ContentVersion::class, 'version_id', 'id');
    }

    /**
     * Contents associated with the version through the pivot.
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_version')
            ->withPivot(['id', 'value'])
            ->withTimestamps();
    }
}
