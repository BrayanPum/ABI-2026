<?php

namespace App\Models\ResearchStaff;

use App\Models\ContentFrameworkProject;

/**
 * Extended model that limits content-framework-project access to the
 * research staff connection, enforcing their reduced privileges.
 */
class ResearchStaffContentFrameworkProject extends ContentFrameworkProject
{
    protected $table = 'content_framework_project';

    protected $connection = 'mysql_research_staff';
}