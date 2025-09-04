<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqliteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          // Path to your SQL file
        $sqlFile = database_path('wintecs.sql');

        // Read SQL file contents
        $sql = file_get_contents($sqlFile);

        // Split into individual statements (optional, SQLite prefers single statements)
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                DB::statement($statement);
            }
        }

        $this->command->info('SQLite SQL file imported successfully!');
    }
}
