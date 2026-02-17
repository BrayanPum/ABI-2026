<?php

namespace App\Models\ResearchStaff;

use App\Models\Program;

/**
 * Extended model that keeps program queries under the research staff
 * connection so actions respect their database permissions.
 */
class ResearchStaffProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_research_staff';
}
