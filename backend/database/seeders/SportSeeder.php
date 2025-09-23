<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            ['name' => 'Archery', 'description' => 'Target archery events (various distances) following World Archery rules', 'category' => 'other', 'icon' => '🏹', 'status' => 'active', 'age_categories' => [['name' => 'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Athletics (Track and Field)', 'description' => 'Track and field events: running, jumping and throwing', 'category' => 'track_field', 'icon' => '🏃', 'status' => 'active', 'age_categories' => [['name' => 'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Badminton', 'description' => 'Singles and doubles badminton events following BWF rules', 'category' => 'racket_sports', 'icon' => '🏸', 'status' => 'active', 'age_categories' => [['name' => 'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Baseball', 'description' => 'Team baseball following standard baseball rules', 'category' => 'team_sport', 'icon' => '⚾', 'status' => 'active', 'age_categories' => [['name' => 'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Basketball', 'description' => 'Team basketball played under FIBA-like rules', 'category' => 'team_sport', 'icon' => '🏀', 'status' => 'active', 'age_categories' => [['name' => 'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Beach Volleyball', 'description' => 'Beach volleyball played on sand (pairs or 3-player formats)', 'category' => 'team_sport', 'icon' => '🏐', 'status' => 'active', 'age_categories' => [['name' => 'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Boxing', 'description' => 'Amateur boxing across weight classes', 'category' => 'combat_sport', 'icon' => '🥊', 'status' => 'active', 'age_categories' => [['name' => 'Youth','max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Chess', 'description' => 'Individual and team chess competitions following FIDE rules', 'category' => 'other', 'icon' => '♟️', 'status' => 'active', 'age_categories' => [['name' => 'Youth','max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Dance Sports', 'description' => 'Competitive dance sports (Latin and Standard) — preserved as requested', 'category' => 'other', 'icon' => '💃', 'status' => 'active', 'age_categories' => [['name' => 'Youth','max_age'=>18],['name'=>'Senior','min_age'=>19]], 'sport_events' => []],
            ['name' => 'Artistic Gymnastics','description'=>"Artistic Gymnastics (men's & women's disciplines)",'category'=>'gymnastics','icon'=>'🤸','status'=>'active','age_categories'=>[['name'=>'Junior','min_age'=>12,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Rhythmic Gymnastics','description'=>'Rhythmic gymnastics demonstrative discipline','category'=>'gymnastics','icon'=>'🤸','status'=>'active','age_categories'=>[['name'=>'Junior','min_age'=>12,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Aero Dynamics','description'=>'Aerodynamic / aerobics demonstrative event','category'=>'gymnastics','icon'=>'🤸','status'=>'active','age_categories'=>[['name'=>'Junior','min_age'=>12,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Billiards','description'=>'Cue sports (rules to be announced)','category'=>'other','icon'=>'🎱','status'=>'inactive','age_categories'=>[],'sport_events'=>[]],
            ['name' => 'Football (Soccer)','description'=>'Team football (soccer) following FIFA rules','category'=>'team_sport','icon'=>'⚽','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Gymnastics','description'=>'Gymnastics including artistic, rhythmic and aerobic disciplines','category'=>'gymnastics','icon'=>'🤸','status'=>'active','age_categories'=>[['name'=>'Junior','min_age'=>12,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Handball','description'=>'Team handball competitions','category'=>'team_sport','icon'=>'🤾','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Karate','description'=>'Karate (kumite and kata) — includes Karate-do entries merged here','category'=>'martial_arts','icon'=>'🥋','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Sepak Takraw','description'=>'Sepak Takraw team/regu/quadrant events','category'=>'team_sport','icon'=>'⚽','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Softball','description'=>'Softball events (team) following ISF rules','category'=>'team_sport','icon'=>'⚾','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Swimming','description'=>'Competitive swimming (individual and relays) following FINA rules','category'=>'aquatics','icon'=>'🏊','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Table Tennis','description'=>'Table tennis singles, doubles and team events','category'=>'racket_sports','icon'=>'🏓','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Taekwondo','description'=>'Taekwondo kyorugi and poomsae competitions','category'=>'martial_arts','icon'=>'🥋','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Tennis','description'=>'Singles and doubles tennis events following ITF rules','category'=>'racket_sports','icon'=>'🎾','status'=>'active','age_categories'=>[['name'=>'Junior','max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Volleyball','description'=>'Indoor volleyball events','category'=>'team_sport','icon'=>'🏐','status'=>'active','age_categories'=>[['name'=>'U12-U15','min_age'=>12,'max_age'=>15],['name'=>'U16-U18','min_age'=>16,'max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
            ['name' => 'Weightlifting','description'=>'Olympic weightlifting: snatch and clean & jerk','category'=>'weightlifting','icon'=>'🏋️','status'=>'active','age_categories'=>[['name'=>'Junior','max_age'=>18],['name'=>'Senior','min_age'=>19]],'sport_events'=>[]],
        ];

        foreach ($sports as $sport) {
            Sport::updateOrCreate(['name' => $sport['name']], $sport);
        }
    }
}