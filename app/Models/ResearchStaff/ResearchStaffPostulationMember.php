<?php

namespace App\Models\ResearchStaff;

use App\Models\PostulationMember;

class ResearchStaffPostulationMember extends PostulationMember
{
    protected $table = 'postulation_members';

    protected $connection = 'mysql_research_staff';
}
