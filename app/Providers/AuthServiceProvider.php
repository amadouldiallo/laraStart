<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        /* check if is admin */
        Gate::define('isAdmin',function($user){
            return $user->type === 'admin';
        });
        /* check if is admin */
        Gate::define('isUser',function($user){
            return $user->type === 'user';
        });
        /* check if is admin */
        Gate::define('isStudent',function($user){
            return $user->type === 'student';
        });
        /* check if is admin */
        Gate::define('isTeacher',function($user){
            return $user->type === 'teacher';
        });

        Passport::routes();
    }
}
