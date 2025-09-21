<?php

use App\Http\Controllers\AdminProfilesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GameMatchController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\MedalTallyController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\PrisaaYearController;
use App\Http\Controllers\OverallChampionController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Public data routes (read-only access)
Route::prefix('public')->group(function () {
    // Schools
    Route::get('/schools', [SchoolController::class, 'index']);
    Route::get('/schools/{school}', [SchoolController::class, 'show']);
    Route::get('/schools/region/{regionId}', [SchoolController::class, 'getByRegion']);
    Route::get('/schools/statistics/overall', [SchoolController::class, 'getOverallStatistics']);
    
    // Sports
    Route::get('/sports', [SportController::class, 'index']);
    Route::get('/sports/{sport}', [SportController::class, 'show']);
    Route::get('/sports/category/{category}', [SportController::class, 'getByCategory']);
    
    // Schedules
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/upcoming', [ScheduleController::class, 'getUpcoming']);
    
    // Game Matches
    Route::get('/matches', [GameMatchController::class, 'index']);
    Route::get('/matches/upcoming', [GameMatchController::class, 'getUpcoming']);
    Route::get('/matches/completed', [GameMatchController::class, 'getCompleted']);
    
    // Results
    Route::get('/results', [ResultController::class, 'index']);
    Route::get('/results/recent', [ResultController::class, 'getRecent']);
    
    // Rankings
    Route::get('/rankings', [RankingController::class, 'index']);
    Route::get('/rankings/sport/{sportId}/teams', [RankingController::class, 'getTeamRankings']);
    Route::get('/rankings/sport/{sportId}/individuals', [RankingController::class, 'getIndividualRankings']);
    Route::get('/rankings/top-performers', [RankingController::class, 'getTopPerformers']);
    
    // Medal Tallies
    Route::get('/medals', [MedalTallyController::class, 'index']);
    Route::get('/medals/statistics', [MedalTallyController::class, 'getOverallStatistics']);
    Route::get('/medals/school-ranking', [MedalTallyController::class, 'getSchoolRanking']);
    
    // Tournaments
    Route::get('/tournaments', [TournamentController::class, 'index']);
    Route::get('/tournaments/upcoming', [TournamentController::class, 'getUpcoming']);
    Route::get('/tournaments/ongoing', [TournamentController::class, 'getOngoing']);
    Route::get('/tournaments/completed', [TournamentController::class, 'getCompleted']);
    Route::get('/users/online', [UserController::class, 'getOnlineUsers']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // User routes - Admin only for user management
    Route::prefix('users')->group(function () {
        Route::middleware('permission:view-users')->get('/', [UserController::class, 'index']);
        Route::middleware('permission:create-users')->post('/', [UserController::class, 'store']);
        Route::middleware('permission:view-users')->get('/role/{role}', [UserController::class, 'getByRole']);
        Route::get('/online', [UserController::class, 'getOnlineUsers']); // 
        
        // Profile routes - all authenticated users can access their own profile
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        
        Route::middleware('permission:view-users')->get('/{user}', [UserController::class, 'show']);
        Route::middleware('permission:edit-users')->put('/{user}', [UserController::class, 'update']);
        Route::middleware('permission:delete-users')->delete('/{user}', [UserController::class, 'destroy']);
    });

    // School routes - Admin can manage, Coach and Tournament Manager can view
    Route::prefix('schools')->group(function () {
        Route::middleware('permission:view-schools')->get('/', [SchoolController::class, 'index']);
        Route::middleware('permission:create-schools')->post('/', [SchoolController::class, 'store']);
        Route::middleware('permission:view-schools')->get('/{school}', [SchoolController::class, 'show']);
        Route::middleware('permission:edit-schools')->put('/{school}', [SchoolController::class, 'update']);
        Route::middleware('permission:delete-schools')->delete('/{school}', [SchoolController::class, 'destroy']);
        Route::middleware('permission:view-schools')->get('/{school}/statistics', [SchoolController::class, 'getStatistics']);
        Route::middleware('permission:view-schools')->get('/region/{region}', [SchoolController::class, 'getByRegion']);
    });

    // Athlete routes - Coaches can manage athletes, Admin can do everything
    Route::prefix('athletes')->group(function () {
        Route::middleware('permission:view-athletes')->get('/', [AthleteController::class, 'index']);
        Route::middleware('permission:create-athletes')->post('/', [AthleteController::class, 'store']);
        Route::middleware('permission:view-athletes')->get('/{athlete}', [AthleteController::class, 'show']);
        Route::middleware('permission:edit-athletes')->put('/{athlete}', [AthleteController::class, 'update']);
        Route::middleware('permission:delete-athletes')->delete('/{athlete}', [AthleteController::class, 'destroy']);
        Route::middleware('permission:view-athletes')->get('/school/{schoolId}', [AthleteController::class, 'getBySchool']);
        Route::middleware('permission:view-athletes')->get('/sport/{sportId}', [AthleteController::class, 'getBySport']);
        Route::middleware('permission:view-athletes')->get('/{athlete}/performance', [AthleteController::class, 'getPerformance']);
    });

    // Sport routes - Tournament Managers can manage sports, others can view
    Route::prefix('sports')->group(function () {
        Route::middleware('permission:view-sports')->get('/', [SportController::class, 'index']);
        Route::middleware('permission:create-sports')->post('/', [SportController::class, 'store']);
        Route::middleware('permission:view-sports')->get('/{sport}', [SportController::class, 'show']);
        Route::middleware('permission:edit-sports')->put('/{sport}', [SportController::class, 'update']);
        Route::middleware('permission:delete-sports')->delete('/{sport}', [SportController::class, 'destroy']);
        Route::middleware('permission:view-sports')->get('/category/{category}', [SportController::class, 'getByCategory']);
        Route::middleware('permission:view-sports')->get('/{sport}/statistics', [SportController::class, 'getStatistics']);
    });

    // Team routes - Coaches can manage teams, others can view
    Route::prefix('teams')->group(function () {
        Route::middleware('permission:view-teams')->get('/', [TeamController::class, 'index']);
        Route::middleware('permission:create-teams')->post('/', [TeamController::class, 'store']);
        Route::middleware('permission:view-teams')->get('/{team}', [TeamController::class, 'show']);
        Route::middleware('permission:edit-teams')->put('/{team}', [TeamController::class, 'update']);
        Route::middleware('permission:delete-teams')->delete('/{team}', [TeamController::class, 'destroy']);
        Route::middleware('permission:view-teams')->get('/school/{schoolId}', [TeamController::class, 'getBySchool']);
        Route::middleware('permission:view-teams')->get('/sport/{sportId}', [TeamController::class, 'getBySport']);
        Route::middleware('permission:view-teams')->get('/{team}/statistics', [TeamController::class, 'getStatistics']);
        Route::middleware('permission:edit-teams')->patch('/{team}/performance', [TeamController::class, 'updatePerformance']);
    });

    // Venue routes - Tournament Managers can manage venues, others can view
    Route::prefix('venues')->group(function () {
        Route::middleware('permission:view-venues')->get('/', [VenueController::class, 'index']);
        Route::middleware('permission:create-venues')->post('/', [VenueController::class, 'store']);
        Route::middleware('permission:view-venues')->get('/{venue}', [VenueController::class, 'show']);
        Route::middleware('permission:edit-venues')->put('/{venue}', [VenueController::class, 'update']);
        Route::middleware('permission:delete-venues')->delete('/{venue}', [VenueController::class, 'destroy']);
        Route::middleware('permission:view-venues')->get('/school/{schoolId}', [VenueController::class, 'getBySchool']);
        Route::middleware('permission:view-venues')->get('/type/{type}', [VenueController::class, 'getByType']);
        Route::middleware('permission:view-venues')->get('/{venue}/availability', [VenueController::class, 'getAvailability']);
        Route::middleware('permission:view-venues')->get('/{venue}/statistics', [VenueController::class, 'getStatistics']);
    });

    // Schedule routes - Tournament Managers can manage schedules, others can view
    Route::prefix('schedules')->group(function () {
        Route::middleware('permission:view-schedules')->get('/', [ScheduleController::class, 'index']);
        Route::middleware('permission:create-schedules')->post('/', [ScheduleController::class, 'store']);
        
        // Specific routes must come before parameterized routes
        Route::middleware('permission:view-schedules')->get('/date-range', [ScheduleController::class, 'getByDateRange']);
        Route::middleware('permission:view-schedules')->get('/upcoming', [ScheduleController::class, 'getUpcoming']);
        Route::middleware('permission:view-schedules')->get('/sport/{sportId}', [ScheduleController::class, 'getBySport']);
        Route::middleware('permission:view-schedules')->get('/venue/{venueId}', [ScheduleController::class, 'getByVenue']);
        
        // Parameterized routes come after specific routes
        Route::middleware('permission:view-schedules')->get('/{schedule}', [ScheduleController::class, 'show']);
        Route::middleware('permission:edit-schedules')->put('/{schedule}', [ScheduleController::class, 'update']);
        Route::middleware('permission:delete-schedules')->delete('/{schedule}', [ScheduleController::class, 'destroy']);
        Route::middleware('permission:edit-schedules')->patch('/{schedule}/status', [ScheduleController::class, 'updateStatus']);
    });

    // Game Match routes - Tournament Managers can manage matches, others can view
    Route::prefix('matches')->group(function () {
        Route::middleware('permission:view-matches')->get('/', [GameMatchController::class, 'index']);
        Route::middleware('permission:create-matches')->post('/', [GameMatchController::class, 'store']);
        
        // Specific routes must come before parameterized routes
        Route::middleware('permission:view-matches')->get('/team/{teamId}', [GameMatchController::class, 'getByTeam']);
        Route::middleware('permission:view-matches')->get('/upcoming', [GameMatchController::class, 'getUpcoming']);
        Route::middleware('permission:view-matches')->get('/completed', [GameMatchController::class, 'getCompleted']);
        
        // Parameterized routes come after specific routes
        Route::middleware('permission:view-matches')->get('/{gameMatch}', [GameMatchController::class, 'show']);
        Route::middleware('permission:edit-matches')->put('/{gameMatch}', [GameMatchController::class, 'update']);
        Route::middleware('permission:delete-matches')->delete('/{gameMatch}', [GameMatchController::class, 'destroy']);
        Route::middleware('permission:edit-matches')->patch('/{gameMatch}/score', [GameMatchController::class, 'updateScore']);
    });

    // Official routes - Tournament Managers can manage officials, others can view
    Route::prefix('officials')->group(function () {
        Route::middleware('permission:view-officials')->get('/', [OfficialController::class, 'index']);
        Route::middleware('permission:create-officials')->post('/', [OfficialController::class, 'store']);
        
        // Specific routes must come before parameterized routes
        Route::middleware('permission:view-officials')->get('/sport/{sportId}', [OfficialController::class, 'getBySport']);
        Route::middleware('permission:view-officials')->get('/type/{type}', [OfficialController::class, 'getByType']);
        Route::middleware('permission:view-officials')->get('/certification/{level}', [OfficialController::class, 'getByCertification']);
        Route::middleware('permission:view-officials')->get('/available', [OfficialController::class, 'getAvailable']);
        
        // Parameterized routes come after specific routes
        Route::middleware('permission:view-officials')->get('/{official}', [OfficialController::class, 'show']);
        Route::middleware('permission:edit-officials')->put('/{official}', [OfficialController::class, 'update']);
        Route::middleware('permission:delete-officials')->delete('/{official}', [OfficialController::class, 'destroy']);
    });

    // Ranking routes - Tournament Managers can manage rankings, others can view
    Route::prefix('rankings')->group(function () {
        Route::middleware('permission:view-rankings')->get('/', [RankingController::class, 'index']);
        Route::middleware('permission:create-rankings')->post('/', [RankingController::class, 'store']);
        Route::middleware('permission:view-rankings')->get('/{ranking}', [RankingController::class, 'show']);
        Route::middleware('permission:edit-rankings')->put('/{ranking}', [RankingController::class, 'update']);
        Route::middleware('permission:delete-rankings')->delete('/{ranking}', [RankingController::class, 'destroy']);
        Route::middleware('permission:view-rankings')->get('/sport/{sportId}/teams', [RankingController::class, 'getTeamRankings']);
        Route::middleware('permission:view-rankings')->get('/sport/{sportId}/individuals', [RankingController::class, 'getIndividualRankings']);
        Route::middleware('permission:view-rankings')->get('/top-performers', [RankingController::class, 'getTopPerformers']);
        Route::middleware('permission:edit-rankings')->post('/update', [RankingController::class, 'updateRankings']);
        Route::middleware('permission:view-rankings')->get('/sport/{sportId}/statistics', [RankingController::class, 'getRankingStatistics']);
    });

    // Medal Tally routes - Tournament Managers can manage medals, others can view
    Route::prefix('medals')->group(function () {
        Route::middleware('permission:view-medals')->get('/', [MedalTallyController::class, 'index']);
        Route::middleware('permission:create-medals')->post('/', [MedalTallyController::class, 'store']);
        Route::middleware('permission:view-medals')->get('/{medalTally}', [MedalTallyController::class, 'show']);
        Route::middleware('permission:edit-medals')->put('/{medalTally}', [MedalTallyController::class, 'update']);
        Route::middleware('permission:delete-medals')->delete('/{medalTally}', [MedalTallyController::class, 'destroy']);
        Route::middleware('permission:view-medals')->get('/school/{schoolId}', [MedalTallyController::class, 'getBySchool']);
        Route::middleware('permission:view-medals')->get('/sport/{sportId}', [MedalTallyController::class, 'getBySport']);
        Route::middleware('permission:view-medals')->get('/statistics', [MedalTallyController::class, 'getOverallStatistics']);
        Route::middleware('permission:view-medals')->get('/school-ranking', [MedalTallyController::class, 'getSchoolRanking']);
        Route::middleware('permission:view-medals')->get('/top-performers', [MedalTallyController::class, 'getTopPerformers']);
    });

    // Result routes - Tournament Managers can manage results, others can view
    Route::prefix('results')->group(function () {
        Route::middleware('permission:view-results')->get('/', [ResultController::class, 'index']);
        Route::middleware('permission:create-results')->post('/', [ResultController::class, 'store']);
        Route::middleware('permission:view-results')->get('/{result}', [ResultController::class, 'show']);
        Route::middleware('permission:edit-results')->put('/{result}', [ResultController::class, 'update']);
        Route::middleware('permission:delete-results')->delete('/{result}', [ResultController::class, 'destroy']);
        Route::middleware('permission:view-results')->get('/team/{teamId}', [ResultController::class, 'getByTeam']);
        Route::middleware('permission:view-results')->get('/sport/{sportId}', [ResultController::class, 'getBySport']);
        Route::middleware('permission:view-results')->get('/recent', [ResultController::class, 'getRecent']);
        Route::middleware('permission:view-results')->get('/match/{gameMatchId}/statistics', [ResultController::class, 'getMatchStatistics']);
        Route::middleware('permission:edit-results')->patch('/{result}/verify', [ResultController::class, 'verifyResult']);
        Route::middleware('permission:view-results')->get('/team/{teamId}/performance', [ResultController::class, 'getTeamPerformance']);
    });

    // Tournament routes - Tournament Managers can manage tournaments, others can view
    Route::prefix('tournaments')->group(function () {
        Route::middleware('permission:view-tournaments')->get('/', [TournamentController::class, 'index']);
        Route::middleware('permission:create-tournaments')->post('/', [TournamentController::class, 'store']);
        
        // Specific routes must come before parameterized routes
        Route::middleware('permission:view-tournaments')->get('/sport/{sportId}', [TournamentController::class, 'getBySport']);
        Route::middleware('permission:view-tournaments')->get('/upcoming', [TournamentController::class, 'getUpcoming']);
        Route::middleware('permission:view-tournaments')->get('/ongoing', [TournamentController::class, 'getOngoing']);
        Route::middleware('permission:view-tournaments')->get('/completed', [TournamentController::class, 'getCompleted']);
        
        // Parameterized routes come after specific routes
        Route::middleware('permission:view-tournaments')->get('/{tournament}', [TournamentController::class, 'show']);
        Route::middleware('permission:edit-tournaments')->put('/{tournament}', [TournamentController::class, 'update']);
        Route::middleware('permission:delete-tournaments')->delete('/{tournament}', [TournamentController::class, 'destroy']);
        Route::middleware('permission:edit-tournaments')->post('/{tournament}/register', [TournamentController::class, 'registerParticipant']);
        Route::middleware('permission:edit-tournaments')->patch('/{tournament}/status', [TournamentController::class, 'updateStatus']);
        Route::middleware('permission:view-tournaments')->get('/{tournament}/statistics', [TournamentController::class, 'getStatistics']);
    });

    // PRISAA Year Management - Historical recording system
    Route::prefix('prisaa-years')->group(function () {
        Route::middleware('permission:view-tournaments')->get('/', [PrisaaYearController::class, 'index']); // List all PRISAA years
        Route::middleware('permission:create-tournaments')->post('/', [PrisaaYearController::class, 'store']); // Create new PRISAA year
        Route::middleware('permission:view-tournaments')->get('/statistics', [PrisaaYearController::class, 'getStatistics']); // Get yearly statistics
        Route::middleware('permission:view-tournaments')->get('/{prisaaYear}', [PrisaaYearController::class, 'show']); // Get specific year details
        Route::middleware('permission:edit-tournaments')->put('/{prisaaYear}', [PrisaaYearController::class, 'update']); // Update PRISAA year
        Route::middleware('permission:delete-tournaments')->delete('/{prisaaYear}', [PrisaaYearController::class, 'destroy']); // Delete PRISAA year
        Route::middleware('permission:view-tournaments')->get('/{prisaaYear}/multi-level', [PrisaaYearController::class, 'getMultiLevelBreakdown']); // Multi-level breakdown
    });

    // Overall Champion Management - Multi-level champion tracking
    Route::prefix('overall-champions')->group(function () {
        Route::middleware('permission:view-tournaments')->get('/', [OverallChampionController::class, 'index']); // List all champions
        Route::middleware('permission:create-tournaments')->post('/', [OverallChampionController::class, 'store']); // Create champion record
        Route::middleware('permission:view-tournaments')->get('/by-year/{year}', [OverallChampionController::class, 'getByYear']); // Champions by year
        Route::middleware('permission:view-tournaments')->get('/by-level/{level}', [OverallChampionController::class, 'getByLevel']); // Champions by level
        Route::middleware('permission:view-tournaments')->get('/{overallChampion}', [OverallChampionController::class, 'show']); // Get specific champion
        Route::middleware('permission:edit-tournaments')->put('/{overallChampion}', [OverallChampionController::class, 'update']); // Update champion
        Route::middleware('permission:delete-tournaments')->delete('/{overallChampion}', [OverallChampionController::class, 'destroy']); // Delete champion
    });

    // Legacy route for backwards compatibility
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => 'connected',
        'timestamp' => now()
    ]);
});

// Test permission route (admin only)
Route::middleware(['auth:sanctum', 'permission:view-users'])->get('/test-admin', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    return response()->json([
        'message' => 'Admin access granted!',
        'user' => $user->first_name . ' ' . $user->last_name,
        'roles' => $user->roles->pluck('name'),
        'permissions' => $user->getAllPermissions()->pluck('name')
    ]);
});

// Test coach permission route
Route::middleware(['auth:sanctum', 'permission:view-teams'])->get('/test-coach', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    return response()->json([
        'message' => 'Coach access granted!',
        'user' => $user->first_name . ' ' . $user->last_name,
        'roles' => $user->roles->pluck('name'),
        'permissions' => $user->getAllPermissions()->pluck('name')
    ]);
});

// Admin routes (Admin only access)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']); // Get dashboard statistics

    // Admin Schedule Management
    Route::prefix('schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'adminIndex']); // List all schedules with admin features
        Route::get('/stats', [ScheduleController::class, 'getAdminStats']); // Get schedule statistics
        Route::post('/', [ScheduleController::class, 'store']); // Create new schedule
        Route::get('/{schedule}', [ScheduleController::class, 'show']); // Get specific schedule
        Route::put('/{schedule}', [ScheduleController::class, 'update']); // Update schedule
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy']); // Delete schedule
        Route::patch('/{schedule}/status', [ScheduleController::class, 'updateStatus']); // Update schedule status
    });

    // Admin Profile Management
    Route::prefix('profiles')->group(function () {
        Route::get('/', [AdminProfilesController::class, 'index']); // List all profiles with pagination
        Route::get('/stats', [UserController::class, 'getProfileStats']); // Get profile statistics
        Route::post('/', [UserController::class, 'store']); // Create new profile
        Route::get('/{user}', [UserController::class, 'show']); // Get specific profile
        Route::put('/{user}', [UserController::class, 'update']); // Update profile
        Route::delete('/{type}/{user}', [UserController::class, 'destroy']); // Delete profile by type
    });

    // Admin Tournament Management
    Route::prefix('tournaments')->group(function () {
        Route::get('/', [TournamentController::class, 'adminIndex']); // List all tournaments
        Route::post('/', [TournamentController::class, 'store']); // Create new tournament
        Route::get('/{tournament}', [TournamentController::class, 'show']); // Get specific tournament
        Route::put('/{tournament}', [TournamentController::class, 'update']); // Update tournament
        Route::delete('/{tournament}', [TournamentController::class, 'destroy']); // Delete tournament
        Route::patch('/{tournament}/status', [TournamentController::class, 'updateStatus']); // Update tournament status
    });

    // Admin Sports Management
    Route::prefix('sports')->group(function () {
        Route::get('/', [SportController::class, 'adminIndex']); // List all sports with admin features
        Route::get('/stats', [SportController::class, 'getAdminStats']); // Get sports statistics
        Route::post('/', [SportController::class, 'store']); // Create new sport
        Route::get('/{sport}', [SportController::class, 'show']); // Get specific sport
        Route::put('/{sport}', [SportController::class, 'update']); // Update sport
        Route::delete('/{sport}', [SportController::class, 'destroy']); // Delete sport
        Route::patch('/{sport}/status', [SportController::class, 'updateStatus']); // Update sport status
        Route::get('/category/{category}', [SportController::class, 'getByCategory']); // Get sports by category
    });

    // Admin Venues Management
    Route::prefix('venues')->group(function () {
        Route::get('/', [VenueController::class, 'adminIndex']); // List all venues with admin features
        Route::get('/stats', [VenueController::class, 'getAdminStats']); // Get venues statistics
        Route::post('/', [VenueController::class, 'store']); // Create new venue
        Route::get('/{venue}', [VenueController::class, 'show']); // Get specific venue
        Route::put('/{venue}', [VenueController::class, 'update']); // Update venue
        Route::delete('/{venue}', [VenueController::class, 'destroy']); // Delete venue
        Route::patch('/{venue}/status', [VenueController::class, 'updateStatus']); // Update venue status
        Route::get('/region/{region}', [VenueController::class, 'getByRegion']); // Get venues by region
    });

    // Admin Regions Management
    Route::prefix('regions')->group(function () {
        Route::get('/', [RegionController::class, 'index']); // List all regions
        Route::post('/', [RegionController::class, 'store']); // Create new region
        Route::get('/{region}', [RegionController::class, 'show']); // Get specific region
        Route::put('/{region}', [RegionController::class, 'update']); // Update region
        Route::delete('/{region}', [RegionController::class, 'destroy']); // Delete region
        Route::get('/status/{status}', [RegionController::class, 'getByStatus']); // Get regions by status
        Route::get('/{region}/statistics', [RegionController::class, 'getStatistics']); // Get region statistics
    });

});