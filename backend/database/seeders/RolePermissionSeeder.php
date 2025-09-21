<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // School Management
            'view-schools',
            'create-schools',
            'edit-schools',
            'delete-schools',
            
            // Region Management
            'view-regions',
            'create-regions',
            'edit-regions',
            'delete-regions',
            
            // Tournament Management
            'view-tournaments',
            'create-tournaments',
            'edit-tournaments',
            'delete-tournaments',
            
            // Sport Management
            'view-sports',
            'create-sports',
            'edit-sports',
            'delete-sports',
            
            // Team Management
            'view-teams',
            'create-teams',
            'edit-teams',
            'delete-teams',
            
            // Athlete Management
            'view-athletes',
            'create-athletes',
            'edit-athletes',
            'delete-athletes',
            
            // Official Management
            'view-officials',
            'create-officials',
            'edit-officials',
            'delete-officials',
            
            // Event Management
            'view-events',
            'create-events',
            'edit-events',
            'delete-events',
            
            // Venue Management
            'view-venues',
            'create-venues',
            'edit-venues',
            'delete-venues',
            
            // Category Management
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            
            // Schedule Management
            'view-schedules',
            'create-schedules',
            'edit-schedules',
            'delete-schedules',
            
            // Match Management
            'view-matches',
            'create-matches',
            'edit-matches',
            'delete-matches',
            
            // Result Management
            'view-results',
            'create-results',
            'edit-results',
            'delete-results',
            
            // Award Management
            'view-awards',
            'create-awards',
            'edit-awards',
            'delete-awards',
            
            // Medal Management
            'view-medals',
            'create-medals',
            'edit-medals',
            'delete-medals',
            
            // Ranking Management
            'view-rankings',
            'create-rankings',
            'edit-rankings',
            'delete-rankings',
            
            // System Settings
            'view-settings',
            'edit-settings',
            
            // Reports
            'view-reports',
            'generate-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $coachRole = Role::firstOrCreate(['name' => 'coach']);
        $tournamentManagerRole = Role::firstOrCreate(['name' => 'tournament_manager']);

        // Assign permissions to roles
        
        // Admin - All permissions
        $adminRole->givePermissionTo(Permission::all());

        // Tournament Manager - Sport and tournament related permissions
        $tournamentManagerRole->givePermissionTo([
            'view-tournaments',
            'create-tournaments',
            'edit-tournaments',
            'view-sports',
            'create-sports',
            'edit-sports',
            'view-events',
            'create-events',
            'edit-events',
            'view-venues',
            'create-venues',
            'edit-venues',
            'view-categories',
            'create-categories',
            'edit-categories',
            'view-schedules',
            'create-schedules',
            'edit-schedules',
            'view-matches',
            'create-matches',
            'edit-matches',
            'view-results',
            'create-results',
            'edit-results',
            'view-rankings',
            'create-rankings',
            'edit-rankings',
            'view-awards',
            'create-awards',
            'edit-awards',
            'view-medals',
            'create-medals',
            'edit-medals',
            'view-officials',
            'create-officials',
            'edit-officials',
            'view-reports',
            'generate-reports',
            'view-schools',
            'view-teams',
            'view-athletes',
        ]);

        // Coach - Team and athlete management permissions
        $coachRole->givePermissionTo([
            'view-teams',
            'create-teams',
            'edit-teams',
            'view-athletes',
            'create-athletes',
            'edit-athletes',
            'view-schedules',
            'view-matches',
            'view-results',
            'view-rankings',
            'view-tournaments',
            'view-sports',
            'view-events',
            'view-schools',
            'view-venues',
            'view-officials',
            'view-medals',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
