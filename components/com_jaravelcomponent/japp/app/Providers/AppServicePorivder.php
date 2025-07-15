<?php
namespace JaravelComponent\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Set base URL for Joomla component
        $this->app['url']->forceRootUrl(
            \JUri::base() . 'index.php?option=com_jaravelcomponent'
        );
    }
}