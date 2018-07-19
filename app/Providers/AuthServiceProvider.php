<?php

namespace App\Providers;

use App\LoanContract;
use App\Policies\LoanContractPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        LoanContract::class => LoanContractPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::enableImplicitGrant();


//        Passport::tokensCan([
//            'approve-loan-contracts' => 'Approve loan contracts',
//            'un-approve-loan-contracts' => 'Un approve loan contracts'
//        ]);

        Gate::before(function (User $user) {
            if ($user->is_admin) {
                return true;
            }
        });
    }
}
