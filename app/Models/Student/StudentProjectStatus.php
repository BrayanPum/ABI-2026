<?php

namespace App\Models\Student;

use App\Models\ProjectStatus;

/**
 * Extended model that keeps project status reads on the student connection to
 * respect the privileges assigned to that role.
 */
class StudentProjectStatus extends ProjectStatus
{
    protected $table = 'project_statuses';

    protected $connection = 'mysql_student';
}
