<?php

namespace Database\Seeders;

class CitiesTableSeeder extends CsvUpsertSeeder
{
    protected string $file = '/database/seeders/csvs/cities.csv';

    protected array $uniqueBy = ['id'];
}
