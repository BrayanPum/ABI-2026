<?php

namespace App\Models\Student;

use App\Models\ThematicArea;

/**
 * Extended model that keeps thematic area queries limited to the student
 * connection so learners read only permitted catalog data.
 */
class StudentThematicArea extends ThematicArea
{
    protected $table = 'thematic_areas';

    protected $connection = 'mysql_student';
}
