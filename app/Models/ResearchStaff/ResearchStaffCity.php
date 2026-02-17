<?php

namespace App\Models\ResearchStaff;

use App\Models\City;

/**
 * Extended model that applies the research staff connection when retrieving
 * city records so the limited privileges remain enforced.
 */
class ResearchStaffCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_research_staff';

}