<?php

namespace App\Models\ResearchStaff;

use App\Models\Framework;

/**
 * Extended model that channels framework operations through the research staff
 * connection to honor the reduced permissions of that user.
 */
class ResearchStaffFramework extends Framework
{
    protected $table = 'frameworks';

    protected $connection = 'mysql_research_staff';
}