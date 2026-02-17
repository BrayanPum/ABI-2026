<?php

namespace App\Models\Professor;

use App\Models\User;

/**
 * Extended model that enforces the professor connection when reading users so
 * authentication data is limited to that role.
 */
class ProfessorUser extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_professor';
}
