<?php

namespace App\Services\Projects;

use App\Helpers\AuthUserHelper;

/**
 * Resolves the role context for the authenticated user so controllers and services can share the same checks.
 */
final class RoleContextResolver
{
    public function resolve(bool $allowResearchStaff = false): RoleContext
    {
        $user = AuthUserHelper::fullUser();
        $isProfessor = in_array($user?->role, ['professor', 'committee_leader'], true);
        $isStudent = $user?->role === 'student';
        $isResearchStaff = $user?->role === 'research_staff';

        if (! $isProfessor && ! $isStudent && ! ($allowResearchStaff && $isResearchStaff)) {
            abort(403, 'This action is only available for professors, committee leaders or students.');
        }

        return new RoleContext(
            $user,
            $isProfessor,
            $isStudent,
            $isResearchStaff,
            $user?->role === 'committee_leader'
        );
    }
}
