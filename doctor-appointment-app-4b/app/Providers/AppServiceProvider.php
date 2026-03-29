<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        $livewireTablesOverrides = resource_path('views/vendor/livewire-tables');
        if (is_dir($livewireTablesOverrides)) {
            View::prependNamespace('livewire-tables', $livewireTablesOverrides);
        }
    }
}
