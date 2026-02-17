<?php

namespace App\Models\ResearchStaff;

use App\Models\CityProgram;

/**
 * Extended model that restricts city-program lookups to the research staff
 * connection, applying their scoped permissions.
 */
class ResearchStaffCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_research_staff';
}
