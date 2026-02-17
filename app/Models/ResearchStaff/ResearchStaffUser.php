<?php

namespace App\Models\ResearchStaff;

use App\Models\User;

/**
 * Extended model that runs user queries through the research staff connection
 * so authentication data remains within the permitted scope.
 */
class ResearchStaffUser extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_research_staff';
}
