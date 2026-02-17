<?php

namespace App\Models\Professor;

use App\Models\ResearchStaff;

/**
 * Extended model that keeps research staff access bound to the professor
 * connection, upholding the scoped privileges of that role.
 */
class ProfessorResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_professor';
}
