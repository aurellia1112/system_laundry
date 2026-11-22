<?php

namespace Database\Seeders;

use App\Models\Services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $services = [
            [
                'service_name' => 'Cuci dan Setrika',
                'category' => 'Kiloan',
                'price_per_unit' => 7000,
                'unit_type' => 'Kg',
                'description' => 'Layanan cuci dan setrika pakaian sehari-hari.',
            ],
            [
                'service_name' => 'Cuci Kering',
                'category' => 'Kiloan',
                'price_per_unit' => 5000,
                'unit_type' => 'Kg',
                'description' => 'Layanan pencucian dan pengeringan tanpa setrika.',
            ],
            [
                'service_name' => 'Setrika Saja',
                'category' => 'Kiloan',
                'price_per_unit' => 4000,
                'unit_type' => 'Kg',
                'description' => 'Layanan setrika untuk pakaian bersih.',
            ],
            [
                'service_name' => 'Cuci Express 6 Jam',
                'category' => 'Express',
                'price_per_unit' => 15000,
                'unit_type' => 'Kg',
                'description' => 'Layanan cuci dan setrika dengan waktu pengerjaan 6 jam.',
            ],
            [
                'service_name' => 'Cuci Express 3 Jam',
                'category' => 'Express',
                'price_per_unit' => 20000,
                'unit_type' => 'Kg',
                'description' => 'Layanan cuci dan setrika dengan waktu pengerjaan 3 jam.',
            ],
        ];

        foreach ($services as $service) {
            Services::updateOrCreate(
                ['service_name' => $service['service_name']],
                $service
            );
        }
    }
}
