<?php

namespace App\Models\Professor;

use App\Models\ContentFramework;

/**
 * Extended model that channels content-framework queries through the professor
 * connection to respect faculty-level permissions.
 */
class ProfessorContentFramework extends ContentFramework
{
    protected $table = 'content_frameworks';

    protected $connection = 'mysql_professor';
}
