<?php

namespace App\Models\Professor;

use App\Models\Program;

/**
 * Extended model that keeps program operations under the professor connection,
 * ensuring faculty stay within their permitted access.
 */
class ProfessorProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_professor';
}
