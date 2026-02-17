<?php

namespace App\Models\Student;

use App\Models\Professor;

/**
 * Extended model that restricts professor lookups to the student connection so
 * learners interact only with data exposed to their profile.
 */
class StudentProfessor extends Professor
{
    protected $table = 'professors';

    protected $connection = 'mysql_student';

    /**
     * {@inheritDoc}
     */
    protected function getProjectModelClass(): string
    {
        return StudentProject::class;
    }
}
