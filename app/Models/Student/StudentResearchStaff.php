<?php

namespace App\Models\Student;

use App\Models\ResearchStaff;

/**
 * Extended model that queries research staff through the student connection,
 * keeping access limited to what students are allowed to view.
 */
class StudentResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_student';
}
