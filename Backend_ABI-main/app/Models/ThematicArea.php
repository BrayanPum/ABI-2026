<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * thematic_areas table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class ThematicArea extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'investigation_line_id',
    ];

    /**
     * Get the investigation line that owns the thematic area.
     */
    public function investigationLine()
    {
        return $this->belongsTo(InvestigationLine::class, 'investigation_line_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'thematic_area_id', 'id');
    }

    /**
     * Set the name attribute with proper formatting.
     *
     * Formats the name by removing extra whitespace and applying title case.
     * If the value is null, it remains null.
     *
     * @param string|null $value The name value to set
     * @return void
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = is_null($value)
            ? null
            : Str::of($value)->squish()->title()->toString();
    }

    /**
     * Set the description attribute with proper formatting.
     *
     * Formats the description by removing extra whitespace.
     * If the value is null, it remains null.
     *
     * @param string|null $value The description value to set
     * @return void
     */
    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = is_null($value)
            ? null
            : Str::of($value)->squish()->toString();
    }
}
