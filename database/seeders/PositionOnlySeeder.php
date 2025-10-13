<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class PositionOnlySeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/positions.csv');

        if (! file_exists($csvFile)) {
            $this->command->error('File positions.csv tidak ditemukan!');

            return;
        }

        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data);

        $this->command->info('Memulai proses seeding untuk tabel positions...');

        foreach ($data as $row) {
            if (count($row) < 3 || empty($row[1]) || empty($row[2])) {
                continue;
            }

            $positionTitle = trim($row[1]);
            $holderName = trim(explode('/', trim($row[2]))[0]);

            $user = User::where('name', $holderName)->first();

            if ($user) {
                Position::updateOrCreate(
                    [
                        'position_title' => $positionTitle,
                        'holder_id' => $user->id,
                    ],
                    [
                        'position_title' => $positionTitle,
                        'holder_id' => $user->id,
                        'updated_at' => now(),
                    ]
                );

                $this->command->line("Processed: {$positionTitle} -> {$holderName}");
            } else {
                $this->command->warn("Skipped: User '{$holderName}' tidak ditemukan untuk jabatan '{$positionTitle}'.");
            }

        }

        $this->command->info('Seeding untuk tabel positions selesai.');
    }
}
