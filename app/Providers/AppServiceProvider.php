<?php

namespace App\Providers;

use App\Services\ReportGenerator\Contracts\IReportFactory;
use App\Services\ReportGenerator\Factories\OptimizedReportFactory;
use App\Services\ReportGenerator\ReportService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the report generator factory interface to a concrete implementation.
        // To switch to a different factory (e.g., StandardReportFactory), only this line needs to change.
        $this->app->bind(IReportFactory::class, OptimizedReportFactory::class);

        // The ReportService will be automatically resolved with OptimizedReportFactory
        // because its constructor requires the IReportFactory interface.
        $this->app->bind(ReportService::class, ReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
