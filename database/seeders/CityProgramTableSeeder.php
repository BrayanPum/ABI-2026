<?php

namespace Database\Seeders;

class CityProgramTableSeeder extends CsvUpsertSeeder
{
    protected string $file = '/database/seeders/csvs/city_program.csv';

    protected array $uniqueBy = ['id'];
}
