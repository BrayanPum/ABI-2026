<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\AcademicProcessWindow;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProjectionReportsTest extends TestCase
{
    private ResearchStaffUser $researchStaff;

    protected function setUp(): void
    {
        parent::setUp();

        $databasePath = database_path('testing.sqlite');

        if (! file_exists($databasePath)) {
            touch($databasePath);
        }

        $sqliteConnection = [
            'driver' => 'sqlite',
            'database' => $databasePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ];

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => $sqliteConnection,
            'database.connections.mysql' => $sqliteConnection,
            'database.connections.mysql_user' => $sqliteConnection,
            'database.connections.mysql_research_staff' => $sqliteConnection,
            'database.connections.mysql_professor' => $sqliteConnection,
            'database.connections.mysql_student' => $sqliteConnection,
        ]);

        Artisan::call('migrate:fresh');

        $this->researchStaff = ResearchStaffUser::create([
            'email' => 'projection-staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $activePeriod = AcademicPeriod::create([
            'code' => '2026A',
            'name' => '2026-A',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => AcademicPeriod::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        AcademicPeriod::create([
            'code' => '2026B',
            'name' => '2026-B',
            'start_date' => now()->addMonths(2)->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'status' => AcademicPeriod::STATUS_DRAFT,
            'is_active' => false,
        ]);

        foreach ([
            AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION,
            AcademicProcessWindow::PROCESS_IDEA_DEMAND_PROJECTION,
        ] as $processKey) {
            AcademicProcessWindow::create([
                'academic_period_id' => $activePeriod->id,
                'process_key' => $processKey,
                'name' => $processKey,
                'start_at' => now()->subDay(),
                'end_at' => now()->addDay(),
                'is_enabled' => true,
                'requires_evaluation' => false,
            ]);
        }
    }

    /** @test */
    public function research_staff_can_view_load_projection_reports(): void
    {
        $response = $this->actingAs($this->researchStaff)
            ->get(route('projections.load-projections.index'));

        $response->assertOk();
        $response->assertViewHas('reportModules', function (array $modules): bool {
            return array_key_exists('hours_by_program', $modules)
                && array_key_exists('stage_load_mix', $modules);
        });
        $response->assertViewHas('activeReportKey', 'hours_by_program');
    }

    /** @test */
    public function research_staff_can_view_idea_demand_reports(): void
    {
        $response = $this->actingAs($this->researchStaff)
            ->get(route('projections.idea-demand.index'));

        $response->assertOk();
        $response->assertViewHas('reportModules', function (array $modules): bool {
            return array_key_exists('demand_vs_supply_by_program', $modules)
                && array_key_exists('thematic_bank_balance', $modules);
        });
    }

    /** @test */
    public function research_staff_can_view_student_reports(): void
    {
        $response = $this->actingAs($this->researchStaff)
            ->get(route('projections.students.index'));

        $response->assertOk();
        $response->assertViewHas('reportModules', function (array $modules): bool {
            return array_key_exists('student_stage_distribution', $modules)
                && array_key_exists('student_activity_distribution', $modules);
        });
        $response->assertViewHas('selectedPeriodId');
    }

    /** @test */
    public function research_staff_can_view_professor_reports(): void
    {
        $response = $this->actingAs($this->researchStaff)
            ->get(route('projections.professors.index'));

        $response->assertOk();
        $response->assertViewHas('reportModules', function (array $modules): bool {
            return array_key_exists('missing_ideas_by_professor', $modules)
                && array_key_exists('program_balance', $modules);
        });
    }

    /** @test */
    public function research_staff_can_export_projection_report_as_pdf(): void
    {
        $response = $this->actingAs($this->researchStaff)
            ->get(route('projections.load-projections.index', [
                'report_export' => 'pdf',
            ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
