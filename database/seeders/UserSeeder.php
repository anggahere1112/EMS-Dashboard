<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the system developer user
        User::create([
            'name' => 'System Developer',
            'email' => 'it.sysdev@ems.com',
            'email_verified_at' => now(),
            'password' => Hash::make('qwerty123'),
            'current_team_id' => 1,
        ]);
        //Create the system developer team
        Team::create([
            'user_id' => User::where('email', 'it.sysdev@ems.com')->first()->id,
            'name' => 'System Developer',
            'personal_team' => 1,
        ]);
    }
}