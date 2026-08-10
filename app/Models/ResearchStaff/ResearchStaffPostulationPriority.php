<?php

namespace App\Models\ResearchStaff;

use App\Models\PostulationPriority;

class ResearchStaffPostulationPriority extends PostulationPriority
{
    protected $table = 'postulation_priorities';

    protected $connection = 'mysql_research_staff';
}
