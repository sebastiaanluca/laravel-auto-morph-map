<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap;

use Illuminate\Support\ServiceProvider;
use SebastiaanLuca\AutoMorphMap\Commands\CacheMorphMap;
use SebastiaanLuca\AutoMorphMap\Commands\ClearCachedMorphMap;

class AutoMorphMapServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(
            $this->getConfigurationPath(),
            $this->getShortPackageName()
        );

        $this->commands(
            CacheMorphMap::class,
            ClearCachedMorphMap::class
        );
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->registerPublishableResources();

        app(Mapper::class)->map();
    }

    /**
     * @return void
     */
    private function registerPublishableResources() : void
    {
        $this->publishes([
            $this->getConfigurationPath() => config_path($this->getShortPackageName() . '.php'),
        ], $this->getPackageName() . ' (configuration)');
    }

    /**
     * @return string
     */
    private function getConfigurationPath() : string
    {
        return __DIR__ . '/../config/' . $this->getShortPackageName() . '.php';
    }

    /**
     * @return string
     */
    private function getShortPackageName() : string
    {
        return 'auto-morph-map';
    }

    /**
     * @return string
     */
    private function getPackageName() : string
    {
        return 'laravel-auto-morph-map';
    }
}
