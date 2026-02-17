<?php

namespace App\Models\ResearchStaff;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Professor;

/**
 * Extended model that forces the research staff connection when accessing
 * professor data, ensuring permissions stay within that role.
 */
class ResearchStaffProfessor extends Professor
{
    protected $table = 'professors';

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
