<?php

namespace App\Models\ResearchStaff;

use App\Models\CityProgram;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Extended model that restricts city-program lookups to the research staff
 * connection, applying their scoped permissions.
 */
class ResearchStaffCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_research_staff';

    public function city(): BelongsTo
    {
        return $this->belongsTo(ResearchStaffCity::class, 'city_id', 'id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(ResearchStaffProgram::class, 'program_id', 'id');
    }
}
