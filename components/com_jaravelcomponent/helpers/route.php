<?php
/**
 * Jaravel Component Route Helper
 *
 * @package    JaravelComponent
 * @subpackage Helpers
 */

// No direct access.
defined('_JEXEC') or die;

use Jaravel\Helpers\RouteHelper;

/**
 * JaravelComponent Route Helper
 */
class JaravelcomponentHelperRoute
{
    /**
     * Route helper instance
     */
    private static $helper = null;
    
    /**
     * View to Laravel path mappings
     */
    private static $viewMappings = [
        'home' => '',           // home view maps to root path
        'tasks' => 'tasks',     // tasks view maps to tasks path
        'dashboard' => 'dashboard',
        'search' => 'search',
        'admin' => 'admin/stats'
    ];
    
    /**
     * Get route helper instance
     */
    private static function getHelper()
    {
        if (self::$helper === null) {
            self::$helper = new RouteHelper('com_jaravelcomponent', self::$viewMappings);
        }
        return self::$helper;
    }
    /**
     * Get route for Laravel path with proper menu item ID
     *
     * @param string $path Laravel route path
     * @param array $params Additional parameters
     * @param mixed $language Language tag
     * @return string
     */
    public static function getRoute($path = '', $params = [], $language = null)
    {
        return self::getHelper()->getRoute($path, $params, $language);
    }
    
    /**
     * Get route by view name
     *
     * @param string $view View name
     * @param array $params Additional parameters
     * @param mixed $language Language tag
     * @return string
     */
    public static function getViewRoute($view, $params = [], $language = null)
    {
        return self::getHelper()->getViewRoute($view, $params, $language);
    }
    
    /**
     * Get task route
     *
     * @param int|string|null $taskId Task ID (optional)
     * @param mixed $language Language tag
     * @return string
     */
    public static function getTaskRoute($taskId = null, $language = null)
    {
        $path = 'tasks';
        if ($taskId !== null) {
            $path .= '/' . $taskId;
        }
        
        return self::getRoute($path, [], $language);
    }
    
    /**
     * Get task details route
     *
     * @param int|string $taskId Task ID
     * @param mixed $language Language tag
     * @return string
     */
    public static function getTaskDetailsRoute($taskId, $language = null)
    {
        return self::getRoute('tasks/' . $taskId . '/details', [], $language);
    }
    
    /**
     * Get admin stats route
     *
     * @param mixed $language Language tag
     * @return string
     */
    public static function getAdminStatsRoute($language = null)
    {
        return self::getRoute('admin/stats', [], $language);
    }
    
    /**
     * Get dashboard route
     *
     * @param mixed $language Language tag
     * @return string
     */
    public static function getDashboardRoute($language = null)
    {
        return self::getRoute('dashboard', [], $language);
    }
    
    /**
     * Get search route
     *
     * @param array $params Search parameters
     * @param mixed $language Language tag
     * @return string
     */
    public static function getSearchRoute($params = [], $language = null)
    {
        return self::getRoute('search', $params, $language);
    }
    
    /**
     * Check if current view has a menu item
     *
     * @return bool
     */
    public static function hasMenuItemId()
    {
        return self::getHelper()->hasMenuItemId();
    }
}