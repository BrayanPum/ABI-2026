<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use JeroenZwart\CsvSeeder\CsvSeeder;

/**
 * Seeder to load initial data into the database from a CSV file.
 *
 * This seeder uses the laravel-csv-seeder package by jeroenzwart to efficiently import data.
 * It follows the naming convention where the CSV file has the same name as the target table.
 *
 * Key features:
 * - Does not truncate the table before inserting (allows multiple executions)
 * - Uses comma (,) as the default delimiter
 * - The CSV file must be located in /database/seeders/csvs/
 * - There is no need to specify the table name, as it is inferred from the CSV filename
 *
 * Note: If you need to completely reset the data, set $this->truncate to true
 */
class VersionsTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeders/csvs/versions.csv';
        $this->delimiter = ',';
        $this->truncate = false; // ⬅️ Avoid TRUNCATE
    }
    /**
     * Runs the seeder to insert data into the database.
     *
     * This method calls the parent class from laravel-csv-seeder, which handles the import
     * process from the CSV file specified in the constructor.
     */
    public function run(): void
    {
        parent::run();
    }
}
