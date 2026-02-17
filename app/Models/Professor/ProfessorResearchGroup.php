<?php

namespace App\Models\Professor;

use App\Models\ResearchGroup;

/**
 * Extended model that funnels research group queries through the professor
 * connection to respect the permissions granted to faculty.
 */
class ProfessorResearchGroup extends ResearchGroup
{
    protected $table = 'research_groups';

    protected $connection = 'mysql_professor';
}
