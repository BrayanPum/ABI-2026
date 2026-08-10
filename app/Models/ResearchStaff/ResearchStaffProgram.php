<?php

namespace App\Models\ResearchStaff;

use App\Models\Program;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Extended model that keeps program queries under the research staff
 * connection so actions respect their database permissions.
 */
class ResearchStaffProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_research_staff';

    public function researchGroup(): BelongsTo
    {
        return $this->belongsTo(ResearchStaffResearchGroup::class, 'research_group_id', 'id');
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(ResearchStaffCity::class, 'city_program', 'program_id', 'city_id')
            ->withTimestamps();
    }

    public function cityPrograms(): HasMany
    {
        return $this->hasMany(ResearchStaffCityProgram::class, 'program_id', 'id');
    }

    public static function uniqueOptions(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (self $program) => mb_strtolower(trim((string) $program->name)))
            ->map(function (Collection $programs) {
                $primaryProgram = $programs->first();
                $primaryProgram->duplicate_ids = $programs
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                return $primaryProgram;
            })
            ->values();
    }

    public static function equivalentIds(?int $programId): array
    {
        if (! $programId) {
            return [];
        }

        $program = static::query()->find($programId);

        if (! $program || blank($program->name)) {
            return [$programId];
        }

        return static::query()
            ->where('name', $program->name)
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
