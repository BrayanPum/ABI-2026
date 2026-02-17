<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * investigation_lines table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class InvestigationLine extends Model
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
        'research_group_id',
    ];

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

    /**
     * Get the research group that owns the investigation line.
     */
    public function researchGroup()
    {
        return $this->belongsTo(ResearchGroup::class, 'research_group_id', 'id');
    }

    /**
     * Get the thematic areas associated with this investigation line.
     */
    public function thematicAreas()
    {
        return $this->hasMany(ThematicArea::class, 'investigation_line_id', 'id');
    }
}
