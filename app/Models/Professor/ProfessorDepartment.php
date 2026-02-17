<?php

namespace App\Models\Professor;

use App\Models\Department;

/**
 * Extended model that keeps department access on the professor connection so
 * faculty operate under their assigned privileges.
 */
class ProfessorDepartment extends Department
{
    protected $table = 'departments';

    protected $connection = 'mysql_professor';
}
