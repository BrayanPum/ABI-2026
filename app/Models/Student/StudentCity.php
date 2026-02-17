<?php

namespace App\Models\Student;

use App\Models\City;

/**
 * Extended model that limits city queries to the student connection so
 * learners operate strictly within their database permissions.
 */
class StudentCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_student';
}