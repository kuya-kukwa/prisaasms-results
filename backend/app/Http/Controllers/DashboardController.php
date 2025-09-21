<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\School;
use App\Models\Tournament;
use App\Models\Schedule;
use App\Models\Official;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            // Count athletes
            $totalAthletes = Athlete::count();
            $activeAthletes = Athlete::where('status', 'active')->count();
            $injuredAthletes = Athlete::where('status', 'injured')->count();
            $suspendedAthletes = Athlete::where('status', 'suspended')->count();

            // Count schools
            $totalSchools = School::count();
            $activeSchools = School::where('status', 'active')->count();

            // Count tournaments
            $totalTournaments = Tournament::count();
            $activeTournaments = Tournament::where('status', 'ongoing')->count();
            $upcomingTournaments = Tournament::where('status', 'upcoming')->count();

            // Count schedules/events
            $totalSchedules = Schedule::count();
            $upcomingEvents = Schedule::where('event_date', '>=', now())
                ->where('event_date', '<=', now()->addDays(7))
                ->count();

            // Count officials
            $totalOfficials = Official::count();
            $activeOfficials = Official::where('status', 'active')->count();

            // Count coaches (users with coach role)
            $totalCoaches = User::where('role', 'coach')->count();
            $activeCoaches = User::where('role', 'coach')->count(); // All coaches are considered active for now

            // Count tournament managers (users with tournament_manager role)
            $totalTournamentManagers = User::where('role', 'tournament_manager')->count();
            $activeTournamentManagers = User::where('role', 'tournament_manager')->count(); // All tournament managers are considered active for now

            // User statistics
            $totalUsers = User::count();
            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $pendingVerifications = User::whereNull('email_verified_at')->count();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => [
                    'totalAthletes' => $totalAthletes,
                    'activeAthletes' => $activeAthletes,
                    'injuredAthletes' => $injuredAthletes,
                    'suspendedAthletes' => $suspendedAthletes,
                    'totalSchools' => $totalSchools,
                    'activeSchools' => $activeSchools,
                    'totalTournaments' => $totalTournaments,
                    'activeTournaments' => $activeTournaments,
                    'upcomingTournaments' => $upcomingTournaments,
                    'totalSchedules' => $totalSchedules,
                    'upcomingEvents' => $upcomingEvents,
                    'totalOfficials' => $totalOfficials,
                    'activeOfficials' => $activeOfficials,
                    'totalCoaches' => $totalCoaches,
                    'activeCoaches' => $activeCoaches,
                    'totalTournamentManagers' => $totalTournamentManagers,
                    'activeTournamentManagers' => $activeTournamentManagers,
                    'totalUsers' => $totalUsers,
                    'verifiedUsers' => $verifiedUsers,
                    'pendingVerifications' => $pendingVerifications,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
