<?php

namespace App\Models\Student;

use App\Models\Project;

/**
 * Extended model that leverages the student connection so learners access
 * only the permissions granted to their role.
 */
class StudentProject extends Project
{
    protected $table = 'projects';

    protected $connection = 'mysql_student';

    /**
     * {@inheritDoc}
     */
    protected function getStudentModelClass(): string
    {
        return StudentStudent::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getProfessorModelClass(): string
    {
        return StudentProfessor::class;
    }
}
