<?php
/**
 * Jaravel Component Route Helper
 *
 * @package    JaravelComponent
 * @subpackage Helpers
 */

// No direct access.
defined('_JEXEC') or die;

use Jaravel\Helpers\MenuHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;

/**
 * JaravelComponent Route Helper
 */
class JaravelcomponentHelperRoute
{
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
        $url = 'index.php?option=com_jaravelcomponent';
        
        if ($path) {
            $url .= '&path=' . ltrim($path, '/');
        }
        
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        // Find menu item ID
        $itemId = MenuHelper::findMenuItemId('com_jaravelcomponent', $path, $language);
        if ($itemId > 0) {
            $url .= '&Itemid=' . $itemId;
        }
        
        // Add language parameter if multilanguage is enabled
        if ($language && $language !== '*' && Multilanguage::isEnabled()) {
            $url .= '&lang=' . $language;
        }
        
        return $url;
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
        $app = Factory::getApplication();
        $input = $app->input;
        
        // Get current path
        $path = $input->get('path', '', 'string');
        
        // Check if there's a menu item for this path
        $itemId = MenuHelper::findMenuItemId('com_jaravelcomponent', $path);
        
        return $itemId > 0;
    }
}