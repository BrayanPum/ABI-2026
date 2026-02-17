<?php

namespace App\Models\ResearchStaff;

use App\Models\ContentVersion;

/**
 * Extended model that keeps content-version queries on the research staff
 * connection, aligning operations with that role's permissions.
 */
class ResearchStaffContentVersion extends ContentVersion
{
    protected $table = 'content_version';

    protected $connection = 'mysql_research_staff';
}
