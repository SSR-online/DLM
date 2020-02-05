<?php

namespace App\Providers;
use Illuminate\Support\Facades\Blade;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('themecss', function () {
            $files = glob(public_path('css/themes/') . "*.css");
            $links = '';
            foreach($files as $file) {
                $components = explode(DIRECTORY_SEPARATOR, $file);
                $filename = array_pop($components);
                $links .= '<link href="/css/themes/' . $filename . '" rel="stylesheet" />';
            }
            return $links;
            
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
