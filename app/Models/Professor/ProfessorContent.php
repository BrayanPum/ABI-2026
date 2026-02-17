<?php

namespace App\Models\Professor;

use App\Models\Content;

/**
 * Extended model that routes content management through the professor
 * connection, aligning with the permissions of that user.
 */
class ProfessorContent extends Content
{
    protected $table = 'contents';

    protected $connection = 'mysql_professor';
}
