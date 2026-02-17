<?php

namespace App\Services\Projects;

use App\Models\User;

/**
 * Value object that describes the currently authenticated user within the projects module.
 */
final class RoleContext
{
    public function __construct(
        public readonly ?User $user,
        public readonly bool $isProfessor,
        public readonly bool $isStudent,
        public readonly bool $isResearchStaff,
        public readonly bool $isCommitteeLeader,
    ) {
    }
}
