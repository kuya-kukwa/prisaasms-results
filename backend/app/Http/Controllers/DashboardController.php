<?php

namespace App\Http\Controllers;
use App\Models\Layer1\School;
use App\Models\Layer1\Region;
use App\Models\Layer1\Province;
use App\Models\Layer1\Tournament;
use App\Models\Layer1\Venue;

use App\Models\Layer2\Athlete;
use App\Models\Layer2\Team;
use App\Models\Layer2\Sport;
use App\Models\Layer2\SportSubcategory;
use App\Models\Layer2\WeightClass;

use App\Models\Layer3\GameMatch;
use App\Models\Layer3\GameResult;
use App\Models\Layer3\ResultMetric;
use App\Models\Layer3\Schedule;
use App\Models\Layer3\MatchOfficial;

use App\Models\Layer4\MedalTally;
use App\Models\Layer4\MvpAward;

use App\Models\Layer1\User; // stays here since user is still in root
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
            $totalOfficials = User::where('role', 'official')->get()->count();
            $activeOfficials = User::where('role', 'official')->where('status', 'active')->count();

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

            return $this->successResponse([
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
            ], 'Dashboard statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard statistics', [$e->getMessage()], 500);
        }
    }
}
