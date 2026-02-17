<?php

namespace App\Models\ResearchStaff;

use App\Models\ThematicArea;

/**
 * Extended model that reuses the research staff connection when fetching
 * thematic areas, keeping queries within the allowed privileges.
 */
class ResearchStaffThematicArea extends ThematicArea
{
    protected $table = 'thematic_areas';

    protected $connection = 'mysql_research_staff';
}
