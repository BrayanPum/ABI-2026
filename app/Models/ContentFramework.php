<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * content_frameworks table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class ContentFramework extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'content_frameworks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['framework_id','name','description'];

    /**
     * Get the framework associated with this content framework.
     */
    public function framework(): BelongsTo
    {
        return $this->belongsTo(Framework::class, 'framework_id', 'id');
    }

    /**
     * Get the assignments that link this content with projects.
     */
    public function contentFrameworkProjects(): HasMany
    {
        return $this->hasMany(ContentFrameworkProject::class, 'content_framework_id', 'id');
    }
}
