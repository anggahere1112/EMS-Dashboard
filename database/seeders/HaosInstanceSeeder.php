<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\HaosInstance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HaosInstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get entities
        $ksv = Entity::where('name', 'KSV')->first();
        $rmy = Entity::where('name', 'RMY')->first();
        $rco = Entity::where('name', 'RCO')->first();

        // KSV - Kuta Seaview Beach Resort HAOS Instance
        if ($ksv) {
            HaosInstance::create([
                'entity_id' => $ksv->id,
                'name' => 'KSV HAOS',
                'ip_address' => '192.168.89.80',
                'port' => 8123,
                'bearer_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiIxMTRhMDk0MTg5MTk0OGM3YTE0OWM1MjQ0MzZiYWY5ZiIsImlhdCI6MTc1NjUzNjM2NSwiZXhwIjoyMDcxODk2MzY1fQ.o0jVPd2OqwvlazH0S-p83WaHTDxpj_dewGn14iY1wJ0',
                'is_active' => true,
                'last_connected_at' => now()->subMinutes(5),
            ]);
        }

        // RMY - Ramayana Suites & Resort HAOS Instances
        if ($rmy) {
            HaosInstance::create([
                'entity_id' => $rmy->id,
                'name' => 'RMY Hotel Tower HAOS',
                'ip_address' => '192.168.2.100',
                'port' => 8123,
                'bearer_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJybXktaG90ZWwiLCJzdWIiOiJob3RlbC1hZG1pbiIsImlhdCI6MTYzNDU2NzAwMH0.sample_token_rmy_hotel',
                'is_active' => true,
                'last_connected_at' => now()->subMinutes(3),
            ]);

            HaosInstance::create([
                'entity_id' => $rmy->id,
                'name' => 'RMY Conference HAOS',
                'ip_address' => '192.168.2.101',
                'port' => 8123,
                'bearer_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJybXktY29uZiIsInN1YiI6ImNvbmYtYWRtaW4iLCJpYXQiOjE2MzQ1NjcwMDB9.sample_token_rmy_conf',
                'is_active' => false,
                'last_connected_at' => now()->subHours(2),
            ]);
        }

        // RCO - Ramayana & Co HAOS Instances
        if ($rco) {
            HaosInstance::create([
                'entity_id' => $rco->id,
                'name' => 'RCO Office HAOS',
                'ip_address' => '192.168.3.100',
                'port' => 8123,
                'bearer_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJyY28tb2ZmaWNlIiwic3ViIjoib2ZmaWNlLWFkbWluIiwiaWF0IjoxNjM0NTY3MDAwfQ.sample_token_rco_office',
                'is_active' => true,
                'last_connected_at' => now()->subMinutes(15),
            ]);
        }
    }
}
