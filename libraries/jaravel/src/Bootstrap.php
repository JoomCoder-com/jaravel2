<?php

namespace Jaravel;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Joomla\CMS\Factory;

/**
 * Bootstrap class for Jaravel components
 * 
 * Handles the initialization and request processing for Laravel applications
 * running as Joomla components. Each component gets its own isolated Laravel
 * instance while sharing the same Laravel framework library.
 */
class Bootstrap
{
    /**
     * The Laravel application instance
     */
    protected Application $app;
    
    /**
     * Path to the component directory
     */
    protected string $componentPath;
    
    /**
     * PSR-4 namespace for the component
     */
    protected string $componentNamespace;
    
    /**
     * Initialize a new Jaravel component bootstrap
     * 
     * @param string $componentPath Path to the component directory
     * @param string $componentNamespace PSR-4 namespace for the component (e.g., 'JaravelComponent')
     * @param array $additionalServices Optional additional services to register
     */
    public function __construct(string $componentPath, string $componentNamespace, array $additionalServices = [])
    {
        $this->componentPath = $componentPath;
        $this->componentNamespace = $componentNamespace;
        
        $this->createApplication();
        $this->registerNamespace();
        $this->registerServices();
        
        // Register any additional services provided
        if (!empty($additionalServices)) {
            $this->registerAdditionalServices($additionalServices);
        }
    }
    
    /**
     * Create the Laravel application instance
     * 
     * The application root is set to the 'japp' directory within the component
     */
    protected function createApplication(): void
    {
        $this->app = new Application($this->componentPath . '/japp');
    }
    
    /**
     * Register the component's namespace with the autoloader
     * 
     * This allows the component's classes to be autoloaded from the japp/app directory
     */
    protected function registerNamespace(): void
    {
        $loader = require JPATH_LIBRARIES . '/jaravel/vendor/autoload.php';
        $loader->addPsr4($this->componentNamespace . '\\', $this->componentPath . '/japp/app');
    }
    
    /**
     * Register Laravel service providers with component-specific implementations
     * 
     * Binds the HTTP Kernel, Console Kernel, and Exception Handler using
     * the component's namespace
     */
    protected function registerServices(): void
    {
        // HTTP Kernel for handling web requests
        $this->app->singleton(
            HttpKernelContract::class,
            $this->componentNamespace . '\Http\Kernel'
        );
        
        // Console Kernel for artisan commands
        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            $this->componentNamespace . '\Console\Kernel'
        );
        
        // Exception Handler for error handling
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            $this->componentNamespace . '\Exceptions\Handler'
        );
    }
    
    /**
     * Handle the incoming request from Joomla and route it to Laravel
     * 
     * @param array $viewMappings Optional mappings from Joomla views to Laravel routes
     */
    public function handleRequest(array $viewMappings = []): void
    {
        // Get Joomla input
        $jinput = Factory::getApplication()->input;
        
        // Check for direct path parameter first
        $path = $jinput->get('path', '', 'STRING');
        
        // If no path, check for view parameter and map it
        if (!$path) {
            $view = $jinput->get('view', '', 'CMD');
            // Use view mapping if defined, otherwise use view name as path
            $path = isset($viewMappings[$view]) ? $viewMappings[$view] : $view;
        }
        
        // Default to root path if still empty
        $path = $path ?: '/';
        
        // Modify server variables for Laravel routing
        $server = $_SERVER;
        $server['REQUEST_URI'] = '/' . ltrim($path, '/');
        $server['PATH_INFO'] = '/' . ltrim($path, '/');
        
        // Create Laravel request with the correct path
        $request = Request::create(
            '/' . ltrim($path, '/'),
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_REQUEST,
            $_COOKIE,
            $_FILES,
            $server
        );
        
        // Get the HTTP kernel and handle the request
        $kernel = $this->app->make(HttpKernelContract::class);
        
        // Capture output to prevent premature sending
        ob_start();
        $response = $kernel->handle($request);
        ob_end_clean();
        
        // Check response content type
        $contentType = $response->headers->get('Content-Type', 'text/html');
        $isHtml = stripos($contentType, 'text/html') !== false;
        
        if ($isHtml) {
            // HTML responses are rendered within Joomla's template
            echo $response->getContent();
            $kernel->terminate($request, $response);
        } else {
            // Non-HTML responses (JSON, downloads, etc.) bypass Joomla
            $response->send();
            $kernel->terminate($request, $response);
            jexit(); // Joomla exit to prevent further processing
        }
    }
    
    /**
     * Register additional services to the application container
     * 
     * @param array $services Array of service bindings
     * 
     * Format examples:
     * - Simple binding: ['abstract' => 'concrete']
     * - Singleton binding: ['abstract' => ['concrete', 'singleton' => true]]
     * - Closure binding: ['abstract' => function($app) { return new Service(); }]
     */
    protected function registerAdditionalServices(array $services): void
    {
        foreach ($services as $abstract => $concrete) {
            // Handle array configuration for more control
            if (is_array($concrete)) {
                $implementation = $concrete[0] ?? $concrete['concrete'] ?? null;
                $singleton = $concrete['singleton'] ?? $concrete[1] ?? false;
                
                if ($implementation) {
                    if ($singleton) {
                        $this->app->singleton($abstract, $implementation);
                    } else {
                        $this->app->bind($abstract, $implementation);
                    }
                }
            } else {
                // Simple binding
                $this->app->bind($abstract, $concrete);
            }
        }
    }
    
    /**
     * Register a service provider with the application
     * 
     * @param string $provider The service provider class name
     * @return void
     */
    public function registerServiceProvider(string $provider): void
    {
        $this->app->register($provider);
    }
    
    /**
     * Register multiple service providers
     * 
     * @param array $providers Array of service provider class names
     * @return void
     */
    public function registerServiceProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            $this->registerServiceProvider($provider);
        }
    }
    
    /**
     * Bind a service into the container
     * 
     * @param string $abstract The abstract name
     * @param mixed $concrete The concrete implementation
     * @param bool $singleton Whether to bind as singleton
     * @return void
     */
    public function bindService(string $abstract, $concrete, bool $singleton = false): void
    {
        if ($singleton) {
            $this->app->singleton($abstract, $concrete);
        } else {
            $this->app->bind($abstract, $concrete);
        }
    }
    
    /**
     * Get the Laravel application instance
     * 
     * @return Application The Laravel application
     */
    public function getApp(): Application
    {
        return $this->app;
    }
}