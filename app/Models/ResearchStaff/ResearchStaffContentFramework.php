<?php

namespace App\Models\ResearchStaff;

use App\Models\ContentFramework;

/**
 * Extended model that keeps content-framework queries within the research
 * staff connection so permissions remain constrained.
 */
class ResearchStaffContentFramework extends ContentFramework
{
    protected $table = 'content_frameworks';

    protected $connection = 'mysql_research_staff';
}
