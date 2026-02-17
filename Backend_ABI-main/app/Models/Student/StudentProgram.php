<?php

namespace App\Models\Student;

use App\Models\Program;

/**
 * Extended model that executes program queries using the student connection,
 * preventing operations outside the student's privileges.
 */
class StudentProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_student';
}
