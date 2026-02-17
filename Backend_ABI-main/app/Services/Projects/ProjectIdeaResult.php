<?php

namespace App\Services\Projects;

use App\Models\Project;

/**
 * Small data carrier that exposes the persisted project alongside the message displayed to the user.
 */
final class ProjectIdeaResult
{
    public function __construct(
        public readonly Project $project,
        public readonly string $message,
    ) {
    }
}
