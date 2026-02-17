<?php

namespace App\Models\Student;

use App\Models\ContentFrameworkProject;

/**
 * Extended model that executes content-framework-project queries on the
 * student connection to respect the role's limitations.
 */
class StudentContentFrameworkProject extends ContentFrameworkProject
{
    protected $table = 'content_framework_project';

    protected $connection = 'mysql_student';
}