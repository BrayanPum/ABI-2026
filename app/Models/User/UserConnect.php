<?php

namespace App\Models\User;

use App\Models\User;

/**
 * Extended model that uses the generic user connection to expose a limited
 * set of permissions tailored for non-privileged accounts.
 */
class UserConnect extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_user';
}
