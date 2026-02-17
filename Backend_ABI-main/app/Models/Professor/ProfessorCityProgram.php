<?php

namespace App\Models\Professor;

use App\Models\CityProgram;

/**
 * Extended model that reads city-program data through the professor
 * connection, limiting operations to what faculty can access.
 */
class ProfessorCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_professor';
}
