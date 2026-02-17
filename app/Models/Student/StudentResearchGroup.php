<?php

namespace App\Models\Student;

use App\Models\ResearchGroup;

/**
 * Extended model that queries research groups through the student connection
 * so learners stay within their allowed scope.
 */
class StudentResearchGroup extends ResearchGroup
{
    protected $table = 'research_groups';

    protected $connection = 'mysql_student';
}
