<?php

namespace App\Models\Student;

use App\Models\PostulationPriority;

class ResearchStaffPostulationPriority extends PostulationPriority
{
    protected $table = 'postulation_priorities';

    protected $connection = 'mysql_student';
}
