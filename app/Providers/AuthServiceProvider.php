<?php

namespace App\Providers;

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
        'App\Setting' => 'App\Policies\SettingPolicy',
        'App\Module' => 'App\Policies\ModulePolicy',
        'App\Node' => 'App\Policies\NodePolicy',
        'App\QuizBlock' => 'App\Policies\QuizBlockPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
