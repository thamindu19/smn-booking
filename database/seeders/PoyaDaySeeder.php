<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PoyaDay;

class PoyaDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $poyaDays = [
            // 2025
            ['month' => 'Duruthu 2025', 'date' => '2025-01-13'],
            ['month' => 'Navam 2025', 'date' => '2025-02-12'],
            ['month' => 'Medin 2025', 'date' => '2025-03-13'],
            ['month' => 'Bak 2025', 'date' => '2025-04-12'],
            ['month' => 'Vesak 2025', 'date' => '2025-05-12'],
            ['month' => 'Poson 2025', 'date' => '2025-06-10'],
            ['month' => 'Esala 2025', 'date' => '2025-07-10'],
            ['month' => 'Nikini 2025', 'date' => '2025-08-08'],
            ['month' => 'Binara 2025', 'date' => '2025-09-07'],
            ['month' => 'Vap 2025', 'date' => '2025-10-06'],
            ['month' => 'Ill 2025', 'date' => '2025-11-05'],
            ['month' => 'Unduvap 2025', 'date' => '2025-12-04'],

            // 2026
            ['month' => 'Duruthu 2026', 'date' => '2026-01-02'],
            ['month' => 'Navam 2026', 'date' => '2026-02-01'],
            ['month' => 'Medin 2026', 'date' => '2026-03-03'],
            ['month' => 'Bak 2026', 'date' => '2026-04-01'],
            ['month' => 'Vesak 2026', 'date' => '2026-05-01'],
            ['month' => 'Adhi Vesak 2026', 'date' => '2026-05-30'],
            ['month' => 'Poson 2026', 'date' => '2026-06-29'],
            ['month' => 'Esala 2026', 'date' => '2026-07-29'],
            ['month' => 'Nikini 2026', 'date' => '2026-08-27'],
            ['month' => 'Binara 2026', 'date' => '2026-09-26'],
            ['month' => 'Vap 2026', 'date' => '2026-10-25'],
            ['month' => 'Ill 2026', 'date' => '2026-11-24'],
            ['month' => 'Unduvap 2026', 'date' => '2026-12-23'],

            // 2027
            ['month' => 'Duruthu 2027', 'date' => '2027-01-22'],
            ['month' => 'Navam 2027', 'date' => '2027-02-20'],
            ['month' => 'Medin 2027', 'date' => '2027-03-21'],
            ['month' => 'Bak 2027', 'date' => '2027-04-20'],
            ['month' => 'Vesak 2027', 'date' => '2027-05-20'],
            ['month' => 'Poson 2027', 'date' => '2027-06-18'],
            ['month' => 'Esala 2027', 'date' => '2027-07-18'],
            ['month' => 'Nikini 2027', 'date' => '2027-08-16'],
            ['month' => 'Binara 2027', 'date' => '2027-09-15'],
            ['month' => 'Vap 2027', 'date' => '2027-10-15'],
            ['month' => 'Ill 2027', 'date' => '2027-11-13'],
            ['month' => 'Unduvap 2027', 'date' => '2027-12-13'],
        ];

        foreach ($poyaDays as $day) {
            PoyaDay::factory()->create([
                'month' => $day['month'],
                'date' => $day['date'],
                'booking_id' => null,
            ]);
        }
    }
}
