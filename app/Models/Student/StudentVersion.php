<?php

namespace App\Models\Student;

use App\Models\Version;

/**
 * Extended model that forces the student connection for version records so
 * learners operate under their restricted privileges.
 */
class StudentVersion extends Version
{
    protected $table = 'versions';

    protected $connection = 'mysql_student';
}
