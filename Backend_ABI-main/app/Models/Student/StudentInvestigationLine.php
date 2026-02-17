<?php

namespace App\Models\Student;

use App\Models\InvestigationLine;

/**
 * Extended model that restricts investigation line queries to the student
 * connection, ensuring students only read authorized data.
 */
class StudentInvestigationLine extends InvestigationLine
{
    protected $table = 'investigation_lines';

    protected $connection = 'mysql_student';
}
