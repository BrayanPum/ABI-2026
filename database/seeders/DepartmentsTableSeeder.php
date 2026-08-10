<?php

namespace Database\Seeders;

class DepartmentsTableSeeder extends CsvUpsertSeeder
{
    protected string $file = '/database/seeders/csvs/departments.csv';

    protected array $uniqueBy = ['id'];
}
