<?php

namespace App\Services\Projects;

use App\Models\Professor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Provides reusable participant lookups for the project creation experience.
 */
final class ProjectParticipantService
{
    /**
     * Returns the participant payload for the requested identifiers keeping the original collection shape.
     *
     * @param \Illuminate\Support\Collection<int, int>|array<int, int> $ids
     * @return \Illuminate\Support\Collection<int, Professor>
     */
    public function fetchByIds(Collection|array $ids): Collection
    {
        $idList = collect($ids)
            ->filter(static fn ($id) => is_numeric($id))
            ->map(static fn ($id) => (int) $id);

        if ($idList->isEmpty()) {
            return collect();
        }

        return $this->baseQuery()
            ->whereIn('professors.id', $idList)
            ->get();
    }

    /**
     * Performs an unrestricted search optionally excluding the active professor.
     *
     * @return \Illuminate\Support\Collection<int, Professor>
     */
    public function search(?int $excludeProfessorId, string $term): Collection
    {
        $query = $this->baseQuery($excludeProfessorId);

        if ($term !== '') {
            $normalizedTerm = mb_strtolower($term);

            $query->where(static function (Builder $builder) use ($normalizedTerm, $term) {
                $builder->whereRaw('LOWER(professors.name) like ?', ["%{$normalizedTerm}%"])
                    ->orWhereRaw('LOWER(professors.last_name) like ?', ["%{$normalizedTerm}%"])
                    ->orWhere('professors.card_id', 'like', "%{$term}%")
                    ->orWhereRaw('LOWER(professors.mail) like ?', ["%{$normalizedTerm}%"])
                    ->orWhereHas('user', static function (Builder $userQuery) use ($normalizedTerm) {
                        $userQuery->whereRaw('LOWER(email) like ?', ["%{$normalizedTerm}%"]);
                    });
            });
        }

        return $query->get();
    }

    /**
     * Converts a professor model into the shape expected by the UI widgets.
     *
     * @return array{id: int, name: string, document: string|null, email: string|null, program: string|null}
     */
    public function present(Professor $professor): array
    {
        return [
            'id' => $professor->id,
            'name' => trim(($professor->name ?? '') . ' ' . ($professor->last_name ?? '')),
            'document' => $professor->card_id,
            'email' => $professor->mail ?? $professor->user?->email,
            'program' => optional($professor->cityProgram?->program)->name,
        ];
    }

    /**
     * Shared query builder used by both the fetch and search flows.
     */
    private function baseQuery(?int $excludeProfessorId = null): Builder
    {
        return Professor::query()
            ->select('professors.*')
            ->with(['user', 'cityProgram.program'])
            ->whereHas('user', static function (Builder $builder) {
                $builder->whereIn('role', ['professor', 'committee_leader', 'committe_leader']);
            })
            ->whereNull('professors.deleted_at')
            ->when($excludeProfessorId, static function (Builder $builder, int $exclude) {
                $builder->where('professors.id', '!=', $exclude);
            })
            ->orderBy('professors.last_name')
            ->orderBy('professors.name');
    }
}
