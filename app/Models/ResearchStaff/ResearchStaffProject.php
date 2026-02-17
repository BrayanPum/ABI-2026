<?php

namespace App\Models\ResearchStaff;

use App\Models\Project;

/**
 * Extended model that forces the research staff database connection so the
 * role operates under its restricted permissions.
 */
class ResearchStaffProject extends Project
{
    protected $table = 'projects';

    protected $connection = 'mysql_research_staff';

    /**
     * {@inheritDoc}
     */
    protected function getStudentModelClass(): string
    {
        return ResearchStaffStudent::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getProfessorModelClass(): string
    {
        return ResearchStaffProfessor::class;
    }
}
