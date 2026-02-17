<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * program table model, manages communication with the database using the root user,
 * should not be used by any end user,
 * always use an inherited model with the connection specific to each role.
 */
class Program extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'research_group_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'code' => 'integer',
    ];

    /**
     * Set the code attribute after cleaning and extracting numeric value.
     *
     * Removes all non-numeric characters from the input. If the resulting string
     * is empty, sets the code as null. Otherwise, converts it to integer.
     * If the input is null, keeps the code as null.
     *
     * @param mixed $value The raw code value to process
     * @return void
     */
    public function setCodeAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['code'] = null;

            return;
        }

        $numeric = preg_replace('/[^0-9]/', '', (string) $value);

        $this->attributes['code'] = $numeric === '' ? null : (int) $numeric;
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
     * Get the research group that owns the program.
     */
    public function researchGroup()
    {
        return $this->belongsTo(ResearchGroup::class, 'research_group_id', 'id');
    }

    /**
     * Get the cities where this program is offered.
     */
    public function cities()
    {
        return $this->belongsToMany(City::class, 'city_program', 'program_id', 'city_id');
    }
}
