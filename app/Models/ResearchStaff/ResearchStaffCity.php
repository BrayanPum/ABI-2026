<?php

namespace App\Models\ResearchStaff;

use App\Models\City;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Extended model that applies the research staff connection when retrieving
 * city records so the limited privileges remain enforced.
 */
class ResearchStaffCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_research_staff';

    public function department(): BelongsTo
    {
        return $this->belongsTo(ResearchStaffDepartment::class, 'department_id', 'id');
    }

    public function cityPrograms(): HasMany
    {
        return $this->hasMany(ResearchStaffCityProgram::class, 'city_id', 'id');
    }

}
