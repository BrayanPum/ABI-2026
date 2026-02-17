<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

/**
 * Seeder for populating the users table from a CSV file.
 *
 * This seeder reads user data from a CSV file and inserts it into the database.
 * Passwords are securely hashed using Laravel's Hash facade before insertion.
 * 
 * The CSV file is expected to have a header row followed by data rows.
 * Required fields: email, state, role, password.
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Reads the users.csv file, parses its contents, and inserts each record
     * into the 'users' table with properly hashed passwords.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/csvs/users.csv');
        $data = array_map('str_getcsv', file($csvFile));

        $header = array_shift($data); // First row = headers

        foreach ($data as $row) {
            $record = array_combine($header, $row);

            DB::table('users')->insert([
                'email' => $record['email'],
                'state' => $record['state'],
                'role' => $record['role'],
                'password' => Hash::make($record['password']), // Encrypted
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}