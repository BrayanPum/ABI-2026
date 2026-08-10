<?php

namespace App\Models\Professors;

use App\Models\Postulation;

class ResearchStaffPostularion extends Postulation
{
    protected $table = 'postulations';

    protected $connection = 'mysql_professor';
}
