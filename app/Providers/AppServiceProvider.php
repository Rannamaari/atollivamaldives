<?php
namespace App\Providers;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Observers\SlugRedirectObserver;
use App\Support\Seo\SeoManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SeoManager::class, fn () => new SeoManager(new SiteSetting()));
    }

    public function boot(): void
    {
        Accommodation::observe(SlugRedirectObserver::class);
        Post::observe(SlugRedirectObserver::class);

        View::composer('*', function ($view): void {
            $view->with('siteSettings', SiteSetting::current());
        });
    }
}
