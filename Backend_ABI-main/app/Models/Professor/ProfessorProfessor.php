<?php

namespace App\Models\Professor;

use App\Models\Professor;

/**
 * Extended model that enforces the professor connection while accessing the
 * professors table, restricting data operations to that role.
 */
class ProfessorProfessor extends Professor
{
    protected $table = 'professors';

    protected $connection = 'mysql_professor';

    /**
     * {@inheritDoc}
     */
    protected function getProjectModelClass(): string
    {
        return ProfessorProject::class;
    }
}
