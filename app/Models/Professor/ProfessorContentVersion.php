<?php

namespace App\Models\Professor;

use App\Models\ContentVersion;

/**
 * Extended model that keeps content-version access on the professor connection
 * so updates respect the role's privileges.
 */
class ProfessorContentVersion extends ContentVersion
{
    protected $table = 'content_version';

    protected $connection = 'mysql_professor';
}
