<?php

namespace App\Models\Professor;

use App\Models\Project;

/**
 * Extended model that routes queries through the professor connection so
 * professors and committee leaders operate within their allowed scope.
 */
class ProfessorProject extends Project
{
    protected $table = 'projects';

    protected $connection = 'mysql_professor';

    /**
     * {@inheritDoc}
     */
    protected function getStudentModelClass(): string
    {
        return ProfessorStudent::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getProfessorModelClass(): string
    {
        return ProfessorProfessor::class;
    }
}
