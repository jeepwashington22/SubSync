<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\GoogleProvider;

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
        $this->registerGoogleLoginDriver();
    }

    /**
     * Register a separate "google_login" Socialite driver used exclusively for
     * "Sign in with Google", so its redirect URI can differ from the Gmail
     * integration flow's redirect URI (Google requires one URI per flow).
     */
    private function registerGoogleLoginDriver(): void
    {
        /** @var \Laravel\Socialite\SocialiteManager $socialite */
        $socialite = $this->app->make(SocialiteFactory::class);

        $socialite->extend('google_login', fn () => $socialite->buildProvider(
            GoogleProvider::class,
            config('services.google_login'),
        ));
    }
}
