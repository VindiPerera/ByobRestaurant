<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            Room::firstOrCreate(
                ['room_number' => $i],
                [
                    'name' => 'Room ' . $i,
                    'capacity' => 6,
                    'base_price' => 0,
                    'status' => 'available',
                ]
            );
        }
    }
}
