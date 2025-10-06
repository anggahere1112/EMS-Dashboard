<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceUID;
use App\Models\Entity;
use App\Models\HaosInstance;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get entities and instances
        $ksv = Entity::where('name', 'KSV')->first();
        $ksvHaos = HaosInstance::where('name', 'KSV HAOS')->first();
        
        // Get KSV locations
        $mainBuilding = Location::where('entity_id', $ksv->id)->where('name', 'Main Building')->first();
        $groundFloor = Location::where('entity_id', $ksv->id)->where('name', 'Ground Floor')->first();
        $lobby = Location::where('entity_id', $ksv->id)->where('name', 'Lobby')->first();
        $restaurant = Location::where('entity_id', $ksv->id)->where('name', 'Restaurant')->first();
        $villaArea = Location::where('entity_id', $ksv->id)->where('name', 'Villa Area')->first();
        $villa1 = Location::where('entity_id', $ksv->id)->where('name', 'Villa 1')->first();

        if ($ksv && $ksvHaos) {
            // Smoke Sensor 1-KSV (Physical Device)
            $smokeSensor1 = Device::create([
                'haos_instance_id' => $ksvHaos->id,
                'name' => 'Smoke Sensor 1-KSV',
                'device_type' => 'Smoke Detector',
                'physical_device_name' => 'Smoke Sensor 1-KSV',
                'entity_id' => $ksv->id,
                'zone_id' => $mainBuilding->id ?? null,
                'level_id' => $groundFloor->id ?? null,
                'space_id' => $lobby->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smoke Sensor 1-KSV
            DeviceUID::create([
                'device_id' => $smokeSensor1->id,
                'uid' => 'binary_sensor.smoke_sensor_1_ksv',
                'uid_type' => 'binary_sensor',
                'description' => 'Smoke Detection',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smokeSensor1->id,
                'uid' => 'sensor.amount_smoke_sensor_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Smoke Amount',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smokeSensor1->id,
                'uid' => 'sensor.battery_smoke_sensor_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Battery Level',
                'is_primary' => false,
            ]);

            // Smart Plug 1-KSV (Physical Device)
            $smartPlug1 = Device::create([
                'haos_instance_id' => $ksvHaos->id,
                'name' => 'Smart Plug 1-KSV',
                'device_type' => 'Smart Plug',
                'physical_device_name' => 'Smart Plug 1-KSV',
                'entity_id' => $ksv->id,
                'zone_id' => $mainBuilding->id ?? null,
                'level_id' => $groundFloor->id ?? null,
                'space_id' => $restaurant->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smart Plug 1-KSV
            DeviceUID::create([
                'device_id' => $smartPlug1->id,
                'uid' => 'switch.switch_smart_plug_1_ksv',
                'uid_type' => 'switch',
                'description' => 'Power Switch',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smartPlug1->id,
                'uid' => 'sensor.total_energy_smart_plug_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Total Energy Consumption',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smartPlug1->id,
                'uid' => 'sensor.current_power_smart_plug_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Current Power Usage',
                'is_primary' => false,
            ]);

            // Temperature & Humidity Sensor 1-KSV (Physical Device)
            $tempHumiditySensor1 = Device::create([
                'haos_instance_id' => $ksvHaos->id,
                'name' => 'Temperature & Humidity Sensor 1-KSV',
                'device_type' => 'Sensor Suhu',
                'physical_device_name' => 'Temperature & Humidity Sensor 1-KSV',
                'entity_id' => $ksv->id,
                'zone_id' => $mainBuilding->id ?? null,
                'level_id' => $groundFloor->id ?? null,
                'space_id' => $lobby->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Temperature & Humidity Sensor 1-KSV
            DeviceUID::create([
                'device_id' => $tempHumiditySensor1->id,
                'uid' => 'sensor.temperature_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Temperature Reading',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $tempHumiditySensor1->id,
                'uid' => 'sensor.humidity_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Humidity Reading',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $tempHumiditySensor1->id,
                'uid' => 'sensor.battery_temp_humidity_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Battery Level',
                'is_primary' => false,
            ]);

            // Smart Meter 1-KSV (Physical Device)
            $smartMeter1 = Device::create([
                'haos_instance_id' => $ksvHaos->id,
                'name' => 'Smart Meter 1-KSV',
                'device_type' => 'Smart Energy Meter',
                'physical_device_name' => 'Smart Meter 1-KSV',
                'entity_id' => $ksv->id,
                'zone_id' => $villaArea->id ?? null,
                'level_id' => $groundFloor->id ?? null,
                'location_id' => $villa1->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smart Meter 1-KSV
            DeviceUID::create([
                'device_id' => $smartMeter1->id,
                'uid' => 'sensor.total_energy_smart_meter_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Total Energy Consumption',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smartMeter1->id,
                'uid' => 'sensor.current_power_smart_meter_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Current Power Usage',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smartMeter1->id,
                'uid' => 'sensor.voltage_smart_meter_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Voltage Reading',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smartMeter1->id,
                'uid' => 'sensor.current_smart_meter_1_ksv',
                'uid_type' => 'sensor',
                'description' => 'Current Reading',
                'is_primary' => false,
            ]);

            // Local Power Outage 1-KSV (Physical Device)
            $localPowerOutage1 = Device::create([
                'haos_instance_id' => $ksvHaos->id,
                'name' => 'Local Power Outage 1-KSV',
                'device_type' => 'Power Outage Detector',
                'physical_device_name' => 'Local Power Outage 1-KSV',
                'entity_id' => $ksv->id,
                'zone_id' => $mainBuilding->id ?? null,
                'level_id' => $groundFloor->id ?? null,
                'space_id' => $lobby->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Local Power Outage 1-KSV
            DeviceUID::create([
                'device_id' => $localPowerOutage1->id,
                'uid' => 'switch.local_power_outage_1_ksv',
                'uid_type' => 'switch',
                'description' => 'Local Power Outage Switch',
                'is_primary' => true,
            ]);

            // Local Power Outage 2-KSV (Physical Device)
            $localPowerOutage2 = Device::create([
                'haos_instance_id' => $ksvHaos->id,
                'name' => 'Local Power Outage 2-KSV',
                'device_type' => 'Power Outage Detector',
                'physical_device_name' => 'Local Power Outage 2-KSV',
                'entity_id' => $ksv->id,
                'zone_id' => $villaArea->id ?? null,
                'level_id' => $groundFloor->id ?? null,
                'space_id' => $villa1->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Local Power Outage 2-KSV
            DeviceUID::create([
                'device_id' => $localPowerOutage2->id,
                'uid' => 'switch.local_power_outage_2_ksv',
                'uid_type' => 'switch',
                'description' => 'Local Power Outage Switch',
                'is_primary' => true,
            ]);
        }

        // RMY - Ramayana Suites & Resort devices
        $rmy = Entity::where('name', 'RMY')->first();
        $rmyHaos = HaosInstance::where('name', 'RMY Hotel Tower HAOS')->first();
        
        // Get RMY locations
        $hotelTower = Location::where('entity_id', $rmy->id)->where('name', 'Hotel Tower')->first();
        $level5 = Location::where('entity_id', $rmy->id)->where('name', 'Level 5')->first();
        $suite501 = Location::where('entity_id', $rmy->id)->where('name', 'Suite 501')->first();
        $suite502 = Location::where('entity_id', $rmy->id)->where('name', 'Suite 502')->first();
        $conferenceCenter = Location::where('entity_id', $rmy->id)->where('name', 'Conference Center')->first();
        $grandBallroom = Location::where('entity_id', $rmy->id)->where('name', 'Grand Ballroom')->first();

        if ($rmy && $rmyHaos) {
            // Smoke Sensor 1-RMY (Physical Device)
            $smokeSensor1Rmy = Device::create([
                'haos_instance_id' => $rmyHaos->id,
                'name' => 'Smoke Sensor 1-RMY',
                'device_type' => 'Smoke Detector',
                'physical_device_name' => 'Smoke Sensor 1-RMY',
                'entity_id' => $rmy->id,
                'zone_id' => $hotelTower->id ?? null,
                'level_id' => $level5->id ?? null,
                'space_id' => $suite501->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smoke Sensor 1-RMY
            DeviceUID::create([
                'device_id' => $smokeSensor1Rmy->id,
                'uid' => 'binary_sensor.smoke_sensor_1_rmy',
                'uid_type' => 'binary_sensor',
                'description' => 'Smoke Detection',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smokeSensor1Rmy->id,
                'uid' => 'sensor.amount_smoke_sensor_1_rmy',
                'uid_type' => 'sensor',
                'description' => 'Smoke Amount',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smokeSensor1Rmy->id,
                'uid' => 'sensor.battery_smoke_sensor_1_rmy',
                'uid_type' => 'sensor',
                'description' => 'Battery Level',
                'is_primary' => false,
            ]);

            // Smoke Sensor 2-RMY (Physical Device)
            $smokeSensor2Rmy = Device::create([
                'haos_instance_id' => $rmyHaos->id,
                'name' => 'Smoke Sensor 2-RMY',
                'device_type' => 'Smoke Detector',
                'physical_device_name' => 'Smoke Sensor 2-RMY',
                'entity_id' => $rmy->id,
                'zone_id' => $conferenceCenter->id ?? null,
                'level_id' => $conferenceCenter->id ?? null, // Use conference center as level since it's a zone
                'space_id' => $grandBallroom->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smoke Sensor 2-RMY
            DeviceUID::create([
                'device_id' => $smokeSensor2Rmy->id,
                'uid' => 'binary_sensor.smoke_sensor_2_rmy',
                'uid_type' => 'binary_sensor',
                'description' => 'Smoke Detection',
                'is_primary' => true,
            ]);

            // Temperature & Humidity Sensor 1-RMY (Physical Device)
            $tempHumiditySensorRmy = Device::create([
                'haos_instance_id' => $rmyHaos->id,
                'name' => 'Temperature & Humidity Sensor 1-RMY',
                'device_type' => 'Sensor Suhu',
                'physical_device_name' => 'Temperature & Humidity Sensor 1-RMY',
                'entity_id' => $rmy->id,
                'zone_id' => $hotelTower->id ?? null,
                'level_id' => $level5->id ?? null,
                'space_id' => $suite501->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Temperature & Humidity Sensor 1-RMY
            DeviceUID::create([
                'device_id' => $tempHumiditySensorRmy->id,
                'uid' => 'sensor.temperature_1_rmy',
                'uid_type' => 'sensor',
                'description' => 'Temperature Reading',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $tempHumiditySensorRmy->id,
                'uid' => 'sensor.humidity_1_rmy',
                'uid_type' => 'sensor',
                'description' => 'Humidity Reading',
                'is_primary' => false,
            ]);

            // Smart Plug 1-RMY (Physical Device)
            $smartPlugRmy = Device::create([
                'haos_instance_id' => $rmyHaos->id,
                'name' => 'Smart Plug 1-RMY',
                'device_type' => 'Smart Plug',
                'physical_device_name' => 'Smart Plug 1-RMY',
                'entity_id' => $rmy->id,
                'zone_id' => $conferenceCenter->id ?? null,
                'level_id' => $conferenceCenter->id ?? null, // Use conference center as level since it's a zone
                'space_id' => $grandBallroom->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smart Plug 1-RMY
            DeviceUID::create([
                'device_id' => $smartPlugRmy->id,
                'uid' => 'switch.switch_smart_plug_1_rmy',
                'uid_type' => 'switch',
                'description' => 'Power Switch',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smartPlugRmy->id,
                'uid' => 'sensor.total_energy_smart_plug_1_rmy',
                'uid_type' => 'sensor',
                'description' => 'Total Energy Consumption',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smartPlugRmy->id,
                'uid' => 'sensor.current_power_smart_plug_1_rmy',
                'uid_type' => 'sensor',
                'description' => 'Current Power Usage',
                'is_primary' => false,
            ]);
        }

        // RCO - Ramayana & Co devices
        $rco = Entity::where('name', 'RCO')->first();
        $rcoHaos = HaosInstance::where('name', 'RCO Office HAOS')->first();
        
        // Get RCO locations
        $officeBuilding = Location::where('entity_id', $rco->id)->where('name', 'Office Building')->first();
        $floor3 = Location::where('entity_id', $rco->id)->where('name', 'Floor 3')->first();
        $itDepartment = Location::where('entity_id', $rco->id)->where('name', 'IT Department')->first();
        $financeDepartment = Location::where('entity_id', $rco->id)->where('name', 'Finance Department')->first();

        if ($rco && $rcoHaos) {
            // Smoke Sensor 1-RCO (Physical Device)
            $smokeSensorRco = Device::create([
                'haos_instance_id' => $rcoHaos->id,
                'name' => 'Smoke Sensor 1-RCO',
                'device_type' => 'Smoke Detector',
                'physical_device_name' => 'Smoke Sensor 1-RCO',
                'entity_id' => $rco->id,
                'zone_id' => $officeBuilding->id ?? null,
                'level_id' => $floor3->id ?? null,
                'space_id' => $itDepartment->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Smoke Sensor 1-RCO
            DeviceUID::create([
                'device_id' => $smokeSensorRco->id,
                'uid' => 'binary_sensor.smoke_sensor_1_rco',
                'uid_type' => 'binary_sensor',
                'description' => 'Smoke Detection',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smokeSensorRco->id,
                'uid' => 'sensor.amount_smoke_sensor_1_rco',
                'uid_type' => 'sensor',
                'description' => 'Smoke Amount',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smokeSensorRco->id,
                'uid' => 'sensor.battery_smoke_sensor_1_rco',
                'uid_type' => 'sensor',
                'description' => 'Battery Level',
                'is_primary' => false,
            ]);

            // Temperature & Humidity Sensor 1-RCO (Physical Device)
            $tempHumiditySensorRco = Device::create([
                'haos_instance_id' => $rcoHaos->id,
                'name' => 'Temperature & Humidity Sensor 1-RCO',
                'device_type' => 'Sensor Suhu',
                'physical_device_name' => 'Temperature & Humidity Sensor 1-RCO',
                'entity_id' => $rco->id,
                'zone_id' => $officeBuilding->id ?? null,
                'level_id' => $floor3->id ?? null,
                'space_id' => $itDepartment->id ?? null,
                'is_active' => true,
            ]);

            // UIDs for Temperature & Humidity Sensor 1-RCO
            DeviceUID::create([
                'device_id' => $tempHumiditySensorRco->id,
                'uid' => 'sensor.temperature_1_rco',
                'uid_type' => 'sensor',
                'description' => 'Temperature Reading',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $tempHumiditySensorRco->id,
                'uid' => 'sensor.humidity_1_rco',
                'uid_type' => 'sensor',
                'description' => 'Humidity Reading',
                'is_primary' => false,
            ]);

            // Smart Plug 1-RCO (Physical Device)
            $smartPlugRco = Device::create([
                'haos_instance_id' => $rcoHaos->id,
                'name' => 'Smart Plug 1-RCO',
                'device_type' => 'Smart Plug',
                'physical_device_name' => 'Smart Plug 1-RCO',
                'entity_id' => $rco->id,
                'zone_id' => $officeBuilding->id ?? null,
                'level_id' => $floor3->id ?? null,
                'space_id' => $financeDepartment->id ?? null,
                'is_active' => false, // Set as inactive for testing
            ]);

            // UIDs for Smart Plug 1-RCO
            DeviceUID::create([
                'device_id' => $smartPlugRco->id,
                'uid' => 'switch.switch_smart_plug_1_rco',
                'uid_type' => 'switch',
                'description' => 'Power Switch',
                'is_primary' => true,
            ]);

            DeviceUID::create([
                'device_id' => $smartPlugRco->id,
                'uid' => 'sensor.total_energy_smart_plug_1_rco',
                'uid_type' => 'sensor',
                'description' => 'Total Energy Consumption',
                'is_primary' => false,
            ]);

            DeviceUID::create([
                'device_id' => $smartPlugRco->id,
                'uid' => 'sensor.current_power_smart_plug_1_rco',
                'uid_type' => 'sensor',
                'description' => 'Current Power Usage',
                'is_primary' => false,
            ]);
        }

        // Get UNHAS entity and instance
        $unhas = Entity::where('name', 'UNHAS')->first();
        $unhasHaos = HaosInstance::where('name', 'UNHAS HAOS')->first();
        
        // Get UNHAS locations - only proceed if UNHAS entity exists
        if ($unhas) {
            $mainBuildingUnhas = Location::where('entity_id', $unhas->id)->where('name', 'Main Building')->first();
            $firstFloor = Location::where('entity_id', $unhas->id)->where('name', 'First Floor')->first();
            $labRoom = Location::where('entity_id', $unhas->id)->where('name', 'Lab Room')->first();
            $officeRoom = Location::where('entity_id', $unhas->id)->where('name', 'Office Room')->first();

            if ($unhas && $unhasHaos) {
                // Smoke Sensor 1-UNHAS (Physical Device)
                $smokeSensorUnhas = Device::create([
                    'haos_instance_id' => $unhasHaos->id,
                    'name' => 'Smoke Sensor 1-UNHAS',
                    'device_type' => 'smoke_sensor',
                    'physical_device_name' => 'Smoke Sensor 1-UNHAS',
                    'entity_id' => $unhas->id,
                    'zone_id' => $mainBuildingUnhas->id ?? null,
                    'level_id' => $firstFloor->id ?? null,
                    'space_id' => $labRoom->id ?? null,
                    'is_active' => true,
                ]);

                // UIDs for Smoke Sensor 1-UNHAS
                DeviceUID::create([
                    'device_id' => $smokeSensorUnhas->id,
                    'uid' => 'binary_sensor.smoke_sensor_1_unhas',
                    'uid_type' => 'binary_sensor',
                    'description' => 'Smoke Detection',
                    'is_primary' => true,
                ]);

                DeviceUID::create([
                    'device_id' => $smokeSensorUnhas->id,
                    'uid' => 'sensor.amount_smoke_sensor_1_unhas',
                    'uid_type' => 'sensor',
                    'description' => 'Smoke Amount',
                    'is_primary' => false,
                ]);

                DeviceUID::create([
                    'device_id' => $smokeSensorUnhas->id,
                    'uid' => 'sensor.battery_smoke_sensor_1_unhas',
                    'uid_type' => 'sensor',
                    'description' => 'Battery Level',
                    'is_primary' => false,
                ]);

                // Temperature Sensor 1-UNHAS (Physical Device)
                $tempSensorUnhas = Device::create([
                    'haos_instance_id' => $unhasHaos->id,
                    'name' => 'Temperature Sensor 1-UNHAS',
                    'device_type' => 'temperature_sensor',
                    'physical_device_name' => 'Temperature Sensor 1-UNHAS',
                    'entity_id' => $unhas->id,
                    'zone_id' => $mainBuildingUnhas->id ?? null,
                    'level_id' => $firstFloor->id ?? null,
                    'space_id' => $officeRoom->id ?? null,
                    'is_active' => true,
                ]);

                // UIDs for Temperature Sensor 1-UNHAS
                DeviceUID::create([
                    'device_id' => $tempSensorUnhas->id,
                    'uid' => 'sensor.temperature_1_unhas',
                    'uid_type' => 'sensor',
                    'description' => 'Temperature Reading',
                    'is_primary' => true,
                ]);

                DeviceUID::create([
                    'device_id' => $tempSensorUnhas->id,
                    'uid' => 'sensor.battery_temperature_1_unhas',
                    'uid_type' => 'sensor',
                    'description' => 'Battery Level',
                    'is_primary' => false,
                ]);
            }
        }
    }
}
