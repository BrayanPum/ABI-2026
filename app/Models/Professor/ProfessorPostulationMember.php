<?php

namespace App\Models\Professor;

use App\Models\PostulationMember;

class ResearchStaffPostulationMember extends PostulationMember
{
    protected $table = 'postulation_members';

    protected $connection = 'mysql_professor';
}
