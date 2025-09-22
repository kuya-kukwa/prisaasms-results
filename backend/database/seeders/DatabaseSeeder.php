<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call your seeders in the correct order
        $this->call([
            RolePermissionSeeder::class,
            DivisionsSeeder::class,
            SportsSeeder::class,
            RegionsSeeder::class,
            ProvincesSeeder::class,
            SchoolsSeeder::class,
            AthletesSeeder::class,
            SportSubcategoriesSeeder::class,
            TournamentLevelsSeeder::class,
            TournamentsSeeder::class,
        ]);

        // Create sample users
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'email'      => 'admin@prisaa.com',
            'password'   => Hash::make('admin123'),
            'role'       => 'admin',
        ]);

        $coachUser = User::create([
            'first_name' => 'Coach',
            'last_name'  => 'User',
            'email'      => 'coach@prisaa.com',
            'password'   => Hash::make('coach123'),
            'role'       => 'coach',
        ]);

        $tournamentManagerUser = User::create([
            'first_name' => 'Tournament',
            'last_name'  => 'Manager',
            'email'      => 'manager@prisaa.com',
            'password'   => Hash::make('manager123'),
            'role'       => 'tournament_manager',
        ]);

        // Assign roles
        $adminUser->assignRole('admin');
        $coachUser->assignRole('coach');
        $tournamentManagerUser->assignRole('tournament_manager');
    }
}
