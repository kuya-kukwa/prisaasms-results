<?php

namespace Database\Seeders;

use App\Models\Layer1\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RegionSeeder;
use Database\Seeders\ProvinceSeeder;
use Database\Seeders\DivisionSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
        {
            $this->call([
            RolePermissionSeeder::class,
            RegionSeeder::class,
            ProvinceSeeder::class,
            
            ]);

        // Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@prisaa.com'],
            ['first_name'=>'Admin','last_name'=>'User','password'=>Hash::make('admin123'),'role'=>'admin']
        );
        $coachUser = User::firstOrCreate(
            ['email' => 'coach@prisaa.com'],
            ['first_name'=>'Coach','last_name'=>'User','password'=>Hash::make('coach123'),'role'=>'coach']
        );
        $tournamentManagerUser = User::firstOrCreate(
            ['email' => 'manager@prisaa.com'],
            ['first_name'=>'Tournament','last_name'=>'Manager','password'=>Hash::make('manager123'),'role'=>'tournament_manager']
        );

        // Assign roles if using Spatie
        $adminUser->assignRole('admin');
        $coachUser->assignRole('coach');
        $tournamentManagerUser->assignRole('tournament_manager');
            }
}
