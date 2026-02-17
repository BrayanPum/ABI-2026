<?php

namespace App\Models\Student;

use App\Models\Framework;

/**
 * Extended model that executes framework operations on the student
 * connection, aligning with the role's restricted privileges.
 */
class StudentFramework extends Framework
{
    protected $table = 'frameworks';

    protected $connection = 'mysql_student';
}