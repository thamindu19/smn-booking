<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PoyaDay;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PoyaDay::limit(5)->pluck('id')->each(function ($id) {
            Booking::factory()->create([
                'status' => 'rejected',
                'poya_day_id' => $id,
            ]);
        });
        PoyaDay::limit(5)->pluck('id')->each(function ($id) {
            $booking = Booking::factory()->create([
                'status' => 'approved',
                'poya_day_id' => $id,
            ]);
            PoyaDay::find($id)->update([
                'booking_id' => $booking->id,
            ]);
        });
        PoyaDay::orderBy('id')->skip(5)->limit(2)->pluck('id')->each(function ($id) {
            Booking::factory()->create([
                'status' => 'pending',
                'poya_day_id' => $id,
            ]);
        });
    }
}
