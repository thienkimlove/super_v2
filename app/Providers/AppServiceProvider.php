<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $viewComposers = [
            'App\Http\ViewComposers\ViSudoViewComposer' => [
                'layouts.app',
                'visudo.*',
            ],
        ];

        foreach ($viewComposers as $key => $value) {
            view()->composer($value, $key);
        }
    }
}
