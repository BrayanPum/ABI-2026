<?php

namespace App\Models\Professor;

use App\Models\City;

/**
 * Extended model that routes city queries through the professor connection so
 * faculty work inside their permitted data scope.
 */
class ProfessorCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_professor';

}