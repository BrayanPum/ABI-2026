<?php

namespace App\Models\ResearchStaff;

use App\Models\Department;

/**
 * Extended model that confines department access to the research staff
 * connection, ensuring the scoped database rights are applied.
 */
class ResearchStaffDepartment extends Department
{
    protected $table = 'departments';

    protected $connection = 'mysql_research_staff';
}
