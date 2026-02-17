<?php

namespace App\Models\Student;

use App\Models\Content;

/**
 * Extended model that keeps content queries on the student connection so
 * learners adhere to their restricted database permissions.
 */
class StudentContent extends Content
{
    protected $table = 'contents';

    protected $connection = 'mysql_student';
}