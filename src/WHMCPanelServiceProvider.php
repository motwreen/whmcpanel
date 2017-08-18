<?php

namespace Motwreen\WHMCPanel;

use Illuminate\Support\ServiceProvider;
class WHMCPanelServiceProvider extends ServiceProvider
{
    public function register()
    {
    	$config = $this->app->make('config');
        $this->mergeConfigFrom(
            __DIR__ . '/config/whmcpanel.php', 'whmcpanel'
        );

        $this->app->bind('whmcpanel', function() {
            return new WHMCPanel;
        });
    }

    public function boot()
    {
         $this->publishes([
            __DIR__.'/config/whmcpanel.php' => config_path('whmcpanel'),
        ],'whmcpanel');
    }
}