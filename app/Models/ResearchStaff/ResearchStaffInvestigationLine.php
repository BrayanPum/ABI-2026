<?php

namespace App\Models\ResearchStaff;

use App\Models\InvestigationLine;

/**
 * Extended model that keeps investigation line queries within the research
 * staff connection, ensuring they follow that role's restrictions.
 */
class ResearchStaffInvestigationLine extends InvestigationLine
{
    protected $table = 'investigation_lines';

    protected $connection = 'mysql_research_staff';
}
