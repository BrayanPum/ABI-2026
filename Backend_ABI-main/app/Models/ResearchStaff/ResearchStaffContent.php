<?php

namespace App\Models\ResearchStaff;

use App\Models\Content;

/**
 * Extended model that routes content queries through the research staff
 * connection to enforce the limited permissions of that user.
 */
class ResearchStaffContent extends Content
{
    protected $table = 'contents';

    protected $connection = 'mysql_research_staff';
}
