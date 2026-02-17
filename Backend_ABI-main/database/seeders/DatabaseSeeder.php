<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

/**
 * Main database seeder.
 *
 * This seeder serves as the entry point for seeding the application's database.
 * It orchestrates the execution of individual seeders, each responsible for populating
 * a specific table with initial data from CSV files.
 *
 * The seeders follow a consistent pattern:
 * - Each extends CsvSeeder to efficiently import data from CSV.
 * - The CSV file name matches the target table name (convention over configuration).
 * - Data is inserted without truncating existing records (to allow repeated execution).
 *
 * To add a new seeder:
 * 1. Create a corresponding CSV file in /database/seeders/csvs/
 * 2. Create a new seeder class extending CsvSeeder
 * 3. Add the seeder class to the $this->call() array below
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Runs all defined seeders in a specific order to ensure referential integrity.
     * The order is important: tables with foreign key dependencies should be seeded
     * after their referenced tables (e.g., users before user-related data).
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            ResearchStaffTableSeeder::class,
            DepartmentsTableSeeder::class,
            CitiesTableSeeder::class,
            ResearchGroupsTableSeeder::class,
            ProgramsTableSeeder::class,
            CityProgramTableSeeder::class,
            ProfessorsTableSeeder::class,
            StudentsTableSeeder::class,
            InvestigationLinesTableSeeder::class,
            ThematicAreasTableSeeder::class,
            ProjectStatusesTableSeeder::class,
            ProjectsTableSeeder::class,
            ProfessorProjectTableSeeder::class,
            StudentProjectTableSeeder::class,
            FrameworksTableSeeder::class,
            ContentFrameworksTableSeeder::class,
            ContentFrameworkProjectTableSeeder::class,
            VersionsTableSeeder::class,
            ContentsTableSeeder::class,
            ContentVersionTableSeeder::class,
        ]);
    }
}
