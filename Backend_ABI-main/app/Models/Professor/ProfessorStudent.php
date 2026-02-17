<?php

namespace App\Models\Professor;

use App\Models\Student;

/**
 * Extended model that keeps student lookups under the professor connection so
 * assigned faculty only access data allowed for their role.
 */
class ProfessorStudent extends Student
{
    protected $table = 'students';

    protected $connection = 'mysql_professor';

    /**
     * {@inheritDoc}
     */
    protected function getProjectModelClass(): string
    {
        return ProfessorProject::class;
    }
}
