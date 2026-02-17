<?php

namespace App\Models\ResearchStaff;

use App\Models\ResearchGroup;

/**
 * Extended model that executes research group queries via the research staff
 * connection to match the permissions of that database user.
 */
class ResearchStaffResearchGroup extends ResearchGroup
{
    protected $table = 'research_groups';

    protected $connection = 'mysql_research_staff';
}
