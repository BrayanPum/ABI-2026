<?php

namespace App\Models\ResearchStaff;

use App\Models\Version;

/**
 * Extended model that enforces the research staff connection when handling
 * version records so they stay within the role's privileges.
 */
class ResearchStaffVersion extends Version
{
    protected $table = 'versions';

    protected $connection = 'mysql_research_staff';
}
