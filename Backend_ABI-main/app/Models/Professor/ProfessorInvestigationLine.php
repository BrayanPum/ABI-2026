<?php

namespace App\Models\Professor;

use App\Models\InvestigationLine;

/**
 * Extended model that limits investigation line access to the professor
 * connection, matching the permissions configured for faculty.
 */
class ProfessorInvestigationLine extends InvestigationLine
{
    protected $table = 'investigation_lines';

    protected $connection = 'mysql_professor';
}
