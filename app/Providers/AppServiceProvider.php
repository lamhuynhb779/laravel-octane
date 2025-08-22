<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Listeners\WriteLogNewUser;
use App\Services\ElasticSearchService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(ElasticSearchService::class, function ($app) {
            return new ElasticSearchService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
//        Event::listen(
//            UserCreated::class,
//            WriteLogNewUser::class,
//        );
    }
}
