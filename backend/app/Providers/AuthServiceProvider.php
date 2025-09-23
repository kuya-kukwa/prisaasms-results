<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Import Models
use App\Models\Layer2\Sport;
use App\Models\Layer2\SportSubcategory;
use App\Models\Layer2\WeightClass;

// Import Policies
use App\Policies\OfficialAssignmentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Sport::class           => OfficialAssignmentPolicy::class,
        SportSubcategory::class => OfficialAssignmentPolicy::class,
        WeightClass::class     => OfficialAssignmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // You can also define Gates here if needed
        // Gate::define('some-ability', fn ($user) => ...);
    }
}
