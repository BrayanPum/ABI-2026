<?php

namespace App\Models\Student;

use App\Models\Department;

/**
 * Extended model that restricts department records to the student connection,
 * making sure learners only reach authorized data.
 */
class StudentDepartment extends Department
{
    protected $table = 'departments';

    protected $connection = 'mysql_student';
}
