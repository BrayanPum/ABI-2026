<?php

namespace App\Models\Student;

use App\Models\Postulation;

class ResearchStaffPostularion extends Postulation
{
    protected $table = 'postulations';

    protected $connection = 'mysql_student';
}
