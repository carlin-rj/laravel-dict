<?php

namespace Carlin\LaravelDict;

use Illuminate\Support\ServiceProvider;

class DictServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
		$this->registerPublishing();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
		$configPath = __DIR__.'/../config/dict-enum.php';
		$this->mergeConfigFrom($configPath, 'dict-enum');
	}

	private function registerPublishing(): void
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../config/dict-enum.php' => config_path('dict-enum.php'),
			], 'config');
		}
	}

}
