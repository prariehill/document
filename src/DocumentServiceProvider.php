<?php

namespace Temporaries\Document;

use Illuminate\Support\ServiceProvider;
use Temporaries\Document\Console\DocumentGeneratorCommand;

class DocumentServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // merge default config
        $this->mergeConfigFrom(
            __DIR__ . '/config/document.php',
            'temporariesDoc'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/document.php' => config_path('temporariesDoc.php')
        ],'config');

        $this->commands(
            DocumentGeneratorCommand::class
        );
    }
}