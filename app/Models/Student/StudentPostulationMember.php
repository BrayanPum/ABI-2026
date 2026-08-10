<?php

namespace App\Models\Student;

use App\Models\PostulationMember;

class ResearchStaffPostulationMember extends PostulationMember
{
    protected $table = 'postulation_members';

    protected $connection = 'mysql_student';
}
