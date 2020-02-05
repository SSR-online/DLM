<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use View;
use \App\Layout;
use \App\Node;
use \App\ImageBlock;
use \App\FileBlock;
use \App\Observers\NodeObserver;
use \App\Observers\ImageBlockObserver;
use \App\Observers\LayoutObserver;
use Log;
use DB;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Carbon::setLocale(config('app.locale'));

        Layout::created(function ($layout) {
            $layout->isCreated();
        });

        Layout::saved(function ($layout) {
            $layout->isSaved();
        });

        //Check if we're a CLI script, then don't do this, breaks artisan on fresh install.
        if(!\App::runningInConsole()) {
            Node::observe(NodeObserver::class);
            Layout::observe(LayoutObserver::class);
            ImageBlock::observe(ImageBlockObserver::class);
            FileBlock::observe(FileBlockObserver::class);
        }
        // DB::listen(function($query) {
        //     // Log::info(
        //     //     if(array_key_exists('file', $line) && strpos($line['file'], '/app/')!==false) {
        //     //         Log::info($line);
        //     //     }
        //     // }
        //     Log::info(
        //         $query->sql,
        //         $query->bindings,
        //         $query->time
        //     );
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
