<?php

namespace App\Providers;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\MoveRequestToHistoryJob;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));  // Untuk locale, bisa gunakan 'id' untuk Indonesia
        date_default_timezone_set(config('app.timezone'));

        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new MoveRequestToHistoryJob)->everyMinute();
        });
    }
}
