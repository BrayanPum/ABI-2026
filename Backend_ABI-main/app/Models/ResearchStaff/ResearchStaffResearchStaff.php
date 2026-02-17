<?php

namespace App\Models\ResearchStaff;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ResearchStaff;

/**
 * Extended model that forces the research staff connection while reading
 * research staff records, respecting their database privileges.
 */
class ResearchStaffResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_research_staff';

    use SoftDeletes;
}
