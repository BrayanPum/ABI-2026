<?php

namespace App\Models\Student;

use App\Models\Student;

/**
 * Extended model that keeps the student connection active so database
 * permissions remain aligned with the student role.
 */
class StudentStudent extends Student
{
    protected $table = 'students';

    protected $connection = 'mysql_student';

    /**
     * {@inheritDoc}
     */
    protected function getProjectModelClass(): string
    {
        return StudentProject::class;
    }
}
