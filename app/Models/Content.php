<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * content table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class Content extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Roles allowed for content association.
     *
     * These are the user roles permitted to interact with or manage content records.
     *
     * @var array<int, string>
     */
    public const ALLOWED_ROLES = [
        'research_staff',
        'professor',
        'student',
        'committee_leader',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'roles',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * The 'roles' attribute is stored as JSON in the database and cast to an array.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'roles' => 'array',
    ];

    /**
     * Get all content version records associated with this content.
     */
    public function contentVersions(): HasMany
    {
        return $this->hasMany(ContentVersion::class, 'content_id', 'id');
    }

    /**
     * Get the versions of projects where this content has been filled out.
     *
     * This relationship goes through the 'content_version' pivot table and includes
     * additional data (id, value) and timestamps from the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function versions(): BelongsToMany
    {
        return $this->belongsToMany(Version::class, 'content_version')
            ->withPivot(['id', 'value'])
            ->withTimestamps();
    }
}
