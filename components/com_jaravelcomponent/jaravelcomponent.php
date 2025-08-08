<?php
defined('_JEXEC') or die;

// Load Laravel autoloader and Jaravel Bootstrap
require_once JPATH_LIBRARIES . '/jaravel/vendor/autoload.php';

use Jaravel\Bootstrap;

/**
 * View to Laravel Route Mappings
 * 
 * This array maps Joomla view names to Laravel routes. In Joomla, menu items
 * are created through XML configuration files that define views. When a user
 * accesses a menu item, Joomla passes the view name as a parameter.
 * 
 * How it works:
 * 1. Admin creates a menu item in Joomla backend, selecting a view (e.g., 'tasks')
 * 2. Joomla generates URL like: index.php?option=com_jaravelcomponent&view=tasks
 * 3. This mapping translates 'tasks' view to Laravel's '/tasks' route
 * 4. Laravel handles the request using routes defined in japp/routes/web.php
 * 
 * Benefits:
 * - Seamless integration between Joomla's menu system and Laravel routing
 * - No need to modify Joomla core or create physical view files
 * - Menu items automatically route to the correct Laravel controller/action
 * 
 * Example mappings:
 * - 'home' => '' : Maps to Laravel's root route (/)
 * - 'tasks' => 'tasks' : Maps to Laravel's /tasks route
 * - 'admin' => 'admin/stats' : Maps to nested route /admin/stats
 */
$JoomlaViewMappings = [
    'home' => '',               // Joomla 'home' view → Laravel '/' route
    'tasks' => 'tasks',         // Joomla 'tasks' view → Laravel '/tasks' route
    'dashboard' => 'dashboard', // Joomla 'dashboard' view → Laravel '/dashboard' route
    'search' => 'search',       // Joomla 'search' view → Laravel '/search' route
    'admin' => 'admin/stats'    // Joomla 'admin' view → Laravel '/admin/stats' route
];

/**
 * Initialize the component with optional additional services
 * 
 * The third parameter allows registering custom services. Examples:
 * 
 * // Simple service binding
 * $additionalServices = [
 *     'App\Contracts\PaymentGateway' => 'App\Services\StripeGateway',
 * ];
 * 
 * // Singleton binding
 * $additionalServices = [
 *     'App\Services\Cache' => ['App\Services\RedisCache', 'singleton' => true],
 * ];
 * 
 * // Closure binding
 * $additionalServices = [
 *     'custom.service' => function($app) {
 *         return new CustomService($app['config']);
 *     }
 * ];
 */
$bootstrap = new Bootstrap(__DIR__, 'JaravelComponent');

// Example: Register additional service providers after initialization
// $bootstrap->registerServiceProvider('App\Providers\CustomServiceProvider');

// Example: Bind additional services
// $bootstrap->bindService('App\Contracts\Logger', 'App\Services\FileLogger', true);

// Handle the request
$bootstrap->handleRequest($JoomlaViewMappings);