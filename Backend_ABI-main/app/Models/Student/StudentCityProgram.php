<?php

namespace App\Models\Student;

use App\Models\CityProgram;

/**
 * Extended model that accesses city-program data through the student
 * connection, maintaining the permissions granted to learners.
 */
class StudentCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_student';
}
