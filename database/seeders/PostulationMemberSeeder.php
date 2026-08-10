<?php

namespace Database\Seeders;

class PostulationMemberSeeder extends CsvUpsertSeeder
{
    protected string $file = '/database/seeders/csvs/postulation_member.csv';

    protected ?string $table = 'postulation_members';

    protected array $uniqueBy = ['member_id'];
}
