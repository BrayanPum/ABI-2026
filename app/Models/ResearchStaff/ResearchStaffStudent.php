<?php

namespace App\Models\ResearchStaff;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Student;

/**
 * Extended model that ensures student queries run on the research staff
 * connection, preserving the limited privileges for that team.
 */
class ResearchStaffStudent extends Student
{
    protected $table = 'students';

    protected $connection = 'mysql_research_staff';

    use SoftDeletes;

    /**
     * {@inheritDoc}
     */
    protected function getProjectModelClass(): string
    {
        return ResearchStaffProject::class;
    }
}
