<?php

namespace App\Models\ResearchStaff;

use App\Models\ProjectStatus;

/**
 * Extended model that binds project status lookups to the research staff
 * connection so catalog queries respect that role's permissions.
 */
class ResearchStaffProjectStatus extends ProjectStatus
{
    protected $table = 'project_statuses';

    protected $connection = 'mysql_research_staff';
}
