<?php

namespace App\Models\Professor;

use App\Models\ContentFrameworkProject;

/**
 * Extended model that executes content-framework-project operations via the
 * professor connection, matching the privileges of that user.
 */
class ProfessorContentFrameworkProject extends ContentFrameworkProject
{
    protected $table = 'content_framework_project';

    protected $connection = 'mysql_professor';
}
