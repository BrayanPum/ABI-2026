<?php

namespace App\Models\Student;

use App\Models\ContentFramework;

/**
 * Extended model that keeps content-framework operations on the student
 * connection, aligning actions with student-level permissions.
 */
class StudentContentFramework extends ContentFramework
{
    protected $table = 'content_frameworks';

    protected $connection = 'mysql_student';
}
