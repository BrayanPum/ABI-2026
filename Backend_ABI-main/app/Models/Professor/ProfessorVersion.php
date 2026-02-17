<?php

namespace App\Models\Professor;

use App\Models\Version;

/**
 * Extended model that ensures version records are handled via the professor
 * connection, keeping actions within faculty privileges.
 */
class ProfessorVersion extends Version
{
    protected $table = 'versions';

    protected $connection = 'mysql_professor';
}
