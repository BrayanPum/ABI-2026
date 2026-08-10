<?php

namespace Database\Seeders;

class PostulationPrioritySeeder extends CsvUpsertSeeder
{
    protected string $file = '/database/seeders/csvs/postulation_priority.csv';

    protected ?string $table = 'postulation_priorities';

    protected array $uniqueBy = ['priority_id'];
}
