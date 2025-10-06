<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
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

        // KSV - Kuta Seaview Beach Resort Locations
        if ($ksv) {
            // Main Building
            $mainBuilding = Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Main Building',
                'type' => 'zone',
                'parent_id' => null,
                'description' => 'Gedung utama resort dengan lobby, restaurant, dan fasilitas umum',
            ]);

            // Main Building - Levels
            $groundFloor = Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Ground Floor',
                'type' => 'level',
                'parent_id' => $mainBuilding->id,
                'description' => 'Lantai dasar dengan lobby, restaurant, dan spa',
            ]);

            $firstFloor = Location::create([
                'entity_id' => $ksv->id,
                'name' => 'First Floor',
                'type' => 'level',
                'parent_id' => $mainBuilding->id,
                'description' => 'Lantai 1 dengan meeting rooms dan business center',
            ]);

            // Ground Floor - Spaces
            Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Lobby',
                'type' => 'space',
                'parent_id' => $groundFloor->id,
                'description' => 'Area lobby utama dengan reception desk',
            ]);

            Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Restaurant',
                'type' => 'space',
                'parent_id' => $groundFloor->id,
                'description' => 'Restaurant utama dengan kapasitas 150 orang',
            ]);

            // Villa Area
            $villaArea = Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Villa Area',
                'type' => 'zone',
                'parent_id' => null,
                'description' => 'Area villa dengan private pool dan garden',
            ]);

            // Villa Area - Spaces
            Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Villa 1',
                'type' => 'space',
                'parent_id' => $villaArea->id,
                'description' => 'Villa premium dengan private pool',
            ]);

            Location::create([
                'entity_id' => $ksv->id,
                'name' => 'Villa 2',
                'type' => 'space',
                'parent_id' => $villaArea->id,
                'description' => 'Villa deluxe dengan garden view',
            ]);
        }

        // RMY - Ramayana Suites & Resort Locations
        if ($rmy) {
            // Hotel Tower
            $hotelTower = Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Hotel Tower',
                'type' => 'zone',
                'parent_id' => null,
                'description' => 'Menara hotel utama dengan 200 kamar',
            ]);

            // Hotel Tower - Levels
            $level5 = Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Level 5',
                'type' => 'level',
                'parent_id' => $hotelTower->id,
                'description' => 'Lantai 5 dengan suite rooms',
            ]);

            $level10 = Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Level 10',
                'type' => 'level',
                'parent_id' => $hotelTower->id,
                'description' => 'Lantai 10 dengan executive rooms',
            ]);

            // Level 5 - Spaces
            Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Suite 501',
                'type' => 'space',
                'parent_id' => $level5->id,
                'description' => 'Presidential suite dengan balcony',
            ]);

            Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Suite 502',
                'type' => 'space',
                'parent_id' => $level5->id,
                'description' => 'Executive suite dengan city view',
            ]);

            // Conference Center
            $conferenceCenter = Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Conference Center',
                'type' => 'zone',
                'parent_id' => null,
                'description' => 'Pusat konferensi dengan berbagai meeting rooms',
            ]);

            // Conference Center - Spaces
            Location::create([
                'entity_id' => $rmy->id,
                'name' => 'Grand Ballroom',
                'type' => 'space',
                'parent_id' => $conferenceCenter->id,
                'description' => 'Ballroom utama kapasitas 500 orang',
            ]);
        }

        // RCO - Ramayana & Co Locations
        if ($rco) {
            // Office Building
            $officeBuilding = Location::create([
                'entity_id' => $rco->id,
                'name' => 'Office Building',
                'type' => 'zone',
                'parent_id' => null,
                'description' => 'Gedung kantor utama Ramayan & Co',
            ]);

            // Office Building - Levels
            $floor3 = Location::create([
                'entity_id' => $rco->id,
                'name' => 'Floor 3',
                'type' => 'level',
                'parent_id' => $officeBuilding->id,
                'description' => 'Lantai 3 dengan departemen IT dan Finance',
            ]);

            // Floor 3 - Spaces
            Location::create([
                'entity_id' => $rco->id,
                'name' => 'IT Department',
                'type' => 'space',
                'parent_id' => $floor3->id,
                'description' => 'Ruang IT dengan server room',
            ]);

            Location::create([
                'entity_id' => $rco->id,
                'name' => 'Finance Department',
                'type' => 'space',
                'parent_id' => $floor3->id,
                'description' => 'Ruang keuangan dan accounting',
            ]);
        }
    }
}
