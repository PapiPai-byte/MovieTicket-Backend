<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Screen;
use Illuminate\Database\Seeder;

class CinemaScreenSeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cinemasData = [
            [
                'name' => 'Downtown Cineplex',
                'location' => '100 Main Street, Metropolis',
                'address' => '100 Main Street',
                'city' => 'Metropolis',
                'screens' => [
                    ['name' => 'Screen 1 · IMAX', 'type' => 'imax', 'total_rows' => 8, 'seats_per_row' => 10],
                    ['name' => 'Screen 2 · Dolby Atmos', 'type' => 'atmos', 'total_rows' => 8, 'seats_per_row' => 10],
                ],
            ],
            [
                'name' => 'Grand Central Cinema',
                'location' => '450 Broadway Ave, Gotham',
                'address' => '450 Broadway Ave',
                'city' => 'Gotham',
                'screens' => [
                    ['name' => 'Screen 1 · Laser Ultra', 'type' => 'imax', 'total_rows' => 8, 'seats_per_row' => 10],
                    ['name' => 'Screen 2 · Prime', 'type' => 'standard', 'total_rows' => 8, 'seats_per_row' => 10],
                ],
            ],
            [
                'name' => 'Starlight VIP Lounge',
                'location' => '78 Sunset Blvd, Star City',
                'address' => '78 Sunset Blvd',
                'city' => 'Star City',
                'screens' => [
                    ['name' => 'VIP Suite A', 'type' => 'vip', 'total_rows' => 6, 'seats_per_row' => 8],
                ],
            ],
        ];

        foreach ($cinemasData as $cinemaItem) {
            $screens = $cinemaItem['screens'];
            unset($cinemaItem['screens']);

            $cinema = Cinema::updateOrCreate(
                ['name' => $cinemaItem['name']],
                $cinemaItem
            );

            foreach ($screens as $screenItem) {
                $screen = Screen::updateOrCreate(
                    [
                        'cinema_id' => $cinema->id,
                        'name' => $screenItem['name'],
                    ],
                    [
                        'type' => $screenItem['type'],
                        'total_rows' => $screenItem['total_rows'],
                        'seats_per_row' => $screenItem['seats_per_row'],
                        'status' => 'active',
                    ]
                );

                // Auto-generate rows and seats
                $screen->generateSeats();
            }
        }
    }
}

