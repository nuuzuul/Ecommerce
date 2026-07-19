<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('partials.store-navbar', function ($view): void {
            $view->with(
                'navCategories',
                Category::query()->where('is_active', true)->orderBy('name')->get(['name', 'slug'])
            );
        });
    }
}
