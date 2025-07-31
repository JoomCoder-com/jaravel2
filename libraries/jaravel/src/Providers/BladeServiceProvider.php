<?php

namespace Jaravel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Jaravel\Helpers\Url;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * The component name using this provider
     */
    protected string $componentName;

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Get component name from the app namespace
        $this->componentName = $this->getComponentName();

        // Register Blade directive for Joomla URLs
        Blade::directive('jurl', function ($expression) {
            return "<?php echo \Jaravel\Helpers\Url::route('{$this->componentName}', $expression); ?>";
        });

        // Share URL helper with all views
        view()->share('jurl', function($path = '', $params = []) {
            return Url::route($this->componentName, $path, $params);
        });

        // Share component name with all views
        view()->share('componentName', $this->componentName);
    }

    /**
     * Get the component name from the application namespace
     * 
     * @return string
     */
    protected function getComponentName(): string
    {
        // Try to get from config first
        $configName = config('jaravel.component_name');
        if ($configName) {
            return $configName;
        }

        // Fallback: derive from namespace
        $namespace = $this->app->getNamespace();
        
        // Convert namespace like "JaravelComponent\" to "com_jaravelcomponent"
        $name = strtolower(trim($namespace, '\\'));
        
        // Add com_ prefix if not present
        if (!str_starts_with($name, 'com_')) {
            $name = 'com_' . $name;
        }
        
        return $name;
    }
}