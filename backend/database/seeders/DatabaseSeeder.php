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
        // First, seed roles and permissions
        $this->call(RolePermissionSeeder::class);

        // Seed regions data
        $this->call(RegionSeeder::class);

        // Seed schools data
        $this->call(SchoolSeeder::class);

        // Seed sports data
        $this->call(SportSeeder::class);

        // Seed venues data
        $this->call(VenueSeeder::class);

        // Seed tournaments data
        $this->call(TournamentSeeder::class);

        // Create admin user
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'email'      => 'admin@prisaa.com',
            'password'   => Hash::make('admin123'), // bcrypt hash
            'role'       => 'admin',
        ]);

        // Create coach user
        $coachUser = User::create([
            'first_name' => 'Coach',
            'last_name'  => 'User',
            'email'      => 'coach@prisaa.com',
            'password'   => Hash::make('coach123'),
            'role'       => 'coach',
        ]);

        // Create tournament manager user
        $tournamentManagerUser = User::create([
            'first_name' => 'Tournament',
            'last_name'  => 'Manager',
            'email'      => 'manager@prisaa.com',
            'password'   => Hash::make('manager123'),
            'role'       => 'tournament_manager',
        ]);

        // Assign roles to users
        $adminUser->assignRole('admin');
        $coachUser->assignRole('coach');
        $tournamentManagerUser->assignRole('tournament_manager');

        // Seed other data
        $this->call(SchoolSeeder::class);
        $this->call(SportSeeder::class);
    }
}
