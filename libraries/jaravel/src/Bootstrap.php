<?php
namespace Jaravel;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

class Bootstrap
{
    protected $app;
    protected $basePath;
    protected $namespace;

    public function __construct($basePath, $namespace = 'App')
    {
        $this->basePath = $basePath;
        $this->namespace = $namespace;
    }

    /**
     * Bootstrap and run the Laravel application
     */
    public function run()
    {
        // Create application instance
        $this->app = new Application($this->basePath);

        // Laravel 12 uses bootstrap/providers.php
        $this->app->configure('app');

        // Load component's autoloader if exists
        if (file_exists($this->basePath . '/vendor/autoload.php')) {
            require_once $this->basePath . '/vendor/autoload.php';
        }

        // Register component namespace
        // Fix: Use global namespace for JPATH_LIBRARIES
        $loader = require \JPATH_LIBRARIES . '/jaravel/vendor/autoload.php';
        $loader->addPsr4($this->namespace . '\\', $this->basePath . '/app');

        // Bind paths
        $this->bindPaths();

        // Register core services with custom namespace
        $this->registerServices();

        // Bootstrap the application
        $this->bootstrapApplication();

        // Handle the request
        $kernel = $this->app->make(Kernel::class);

        $request = Request::capture();
        $response = $kernel->handle($request);

        $response->send();

        $kernel->terminate($request, $response);
    }

    protected function bindPaths()
    {
        $this->app->bind('path.public', function() {
            return $this->basePath . '/public';
        });

        $this->app->bind('path.storage', function() {
            return $this->basePath . '/storage';
        });

        $this->app->bind('path.resources', function() {
            return $this->basePath . '/resources';
        });

        $this->app->bind('path.config', function() {
            return $this->basePath . '/config';
        });
    }

    protected function registerServices()
    {
        $this->app->singleton(
            Kernel::class,
            $this->namespace . '\\Http\\Kernel'
        );

        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            $this->namespace . '\\Console\\Kernel'
        );

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            $this->namespace . '\\Exceptions\\Handler'
        );
    }

    protected function bootstrapApplication()
    {
        // Load service providers for Laravel 12
        if (file_exists($this->basePath . '/bootstrap/providers.php')) {
            $providers = require $this->basePath . '/bootstrap/providers.php';
            foreach ($providers as $provider) {
                $this->app->register($provider);
            }
        }

        // Load the app bootstrap file
        if (file_exists($this->basePath . '/bootstrap/app.php')) {
            require $this->basePath . '/bootstrap/app.php';
        }

        $this->app->boot();
    }

    public function getApp()
    {
        return $this->app;
    }
}