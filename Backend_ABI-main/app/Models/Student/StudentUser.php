<?php

namespace App\Models\Student;

use App\Models\User;

/**
 * Extended model that ensures user lookups for students use the student
 * connection, applying the limited privileges available to them.
 */
class StudentUser extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_student';
}
