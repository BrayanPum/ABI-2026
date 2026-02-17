<?php

namespace App\Models\Professor;

use App\Models\ProjectStatus;

/**
 * Extended model that performs project status queries via the professor
 * connection so catalog reads adhere to faculty permissions.
 */
class ProfessorProjectStatus extends ProjectStatus
{
    protected $table = 'project_statuses';

    protected $connection = 'mysql_professor';
}
