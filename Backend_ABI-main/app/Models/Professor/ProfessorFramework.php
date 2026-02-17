<?php

namespace App\Models\Professor;

use App\Models\Framework;

/**
 * Extended model that ensures framework queries go through the professor
 * connection, adhering to the role's reduced permissions.
 */
class ProfessorFramework extends Framework
{
    protected $table = 'frameworks';

    protected $connection = 'mysql_professor';
}