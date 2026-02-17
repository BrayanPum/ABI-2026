<?php

namespace App\Models\Professor;

use App\Models\ThematicArea;

/**
 * Extended model that ensures thematic area reads occur through the professor
 * connection so catalog access follows faculty restrictions.
 */
class ProfessorThematicArea extends ThematicArea
{
    protected $table = 'thematic_areas';

    protected $connection = 'mysql_professor';
}
