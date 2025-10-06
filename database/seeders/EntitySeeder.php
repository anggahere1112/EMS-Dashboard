<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = [
            [
                'name' => 'KSV',
                'description' => 'Kuta Seaview Beach Resort',
            ],
            [
                'name' => 'RMY',
                'description' => 'Ramayana Suites & Resort.',
            ],
            [
                'name' => 'RCO',
                'description' => 'Ramayana & Co.',
            ],
        ];

        foreach ($entities as $entity) {
            Entity::create($entity);
        }
    }
}
