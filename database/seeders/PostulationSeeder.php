<?php

namespace Database\Seeders;

class PostulationSeeder extends CsvUpsertSeeder
{
    protected string $file = '/database/seeders/csvs/postulations.csv';

    protected ?string $table = 'postulations';

    protected array $uniqueBy = ['postulation_id'];
}
