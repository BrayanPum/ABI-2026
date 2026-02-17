<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * research_groups table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class ResearchGroup extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'initials',
        'description',
    ];

    /**
     * Get the programs associated with the research group.
     */
    public function programs()
    {
        return $this->hasMany(Program::class, 'research_group_id', 'id');
    }

    /**
     * Get the investigation lines associated with the research group.
     */
    public function investigationLines()
    {
        return $this->hasMany(InvestigationLine::class, 'research_group_id', 'id');
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
     * Set the initials attribute with proper formatting.
     *
     * Formats the initials by removing extra whitespace and converting to uppercase.
     * If the value is null, it remains null.
     *
     * @param string|null $value The initials value to set
     * @return void
     */
    public function setInitialsAttribute($value): void
    {
        $this->attributes['initials'] = is_null($value)
            ? null
            : Str::of($value)->squish()->upper()->toString();
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
