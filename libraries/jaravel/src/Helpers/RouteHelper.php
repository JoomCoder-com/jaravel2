<?php

namespace Jaravel\Helpers;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;

/**
 * Reusable Route Helper for Jaravel Components
 */
class RouteHelper
{
    /**
     * Component name
     */
    protected string $component;
    
    /**
     * View to path mappings
     */
    protected array $viewMappings = [];
    
    /**
     * Constructor
     *
     * @param string $component Component name
     * @param array $viewMappings View to Laravel path mappings
     */
    public function __construct(string $component, array $viewMappings = [])
    {
        $this->component = $component;
        $this->viewMappings = $viewMappings;
    }
    
    /**
     * Get route for Laravel path with proper menu item ID
     *
     * @param string $path Laravel route path
     * @param array $params Additional parameters
     * @param mixed $language Language tag
     * @return string
     */
    public function getRoute($path = '', $params = [], $language = null)
    {
        $url = 'index.php?option=' . $this->component;
        
        if ($path) {
            $url .= '&path=' . ltrim($path, '/');
        }
        
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        // Find menu item ID
        $itemId = MenuHelper::findMenuItemId($this->component, $path, $language);
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
     * Get route by Joomla view name
     *
     * @param string $view Joomla view name
     * @param array $params Additional parameters
     * @param mixed $language Language tag
     * @return string
     */
    public function getViewRoute($view, $params = [], $language = null)
    {
        // Check if view has a mapping
        $path = $this->viewMappings[$view] ?? $view;
        
        return $this->getRoute($path, $params, $language);
    }
    
    /**
     * Get path from current Joomla request
     *
     * @return string
     */
    public function getPathFromRequest()
    {
        $input = Factory::getApplication()->input;
        
        // First check for direct path parameter
        $path = $input->get('path', '', 'string');
        if ($path) {
            return $path;
        }
        
        // Check for view parameter and map it
        $view = $input->get('view', '', 'cmd');
        if ($view && isset($this->viewMappings[$view])) {
            return $this->viewMappings[$view];
        }
        
        // Default to view name if no mapping
        return $view ?: '';
    }
    
    /**
     * Check if current view has a menu item
     *
     * @return bool
     */
    public function hasMenuItemId()
    {
        $path = $this->getPathFromRequest();
        $itemId = MenuHelper::findMenuItemId($this->component, $path);
        
        return $itemId > 0;
    }
    
    /**
     * Create route helper instance with predefined routes
     *
     * @param string $component Component name
     * @param array $viewMappings View to path mappings
     * @return object
     */
    public static function create($component, $viewMappings = [])
    {
        $helper = new self($component, $viewMappings);
        
        return new class($helper) {
            private $helper;
            
            public function __construct($helper)
            {
                $this->helper = $helper;
            }
            
            public function __call($method, $args)
            {
                if (method_exists($this->helper, $method)) {
                    return call_user_func_array([$this->helper, $method], $args);
                }
                
                // Dynamic route methods
                if (str_starts_with($method, 'get') && str_ends_with($method, 'Route')) {
                    $view = strtolower(substr($method, 3, -5));
                    return $this->helper->getViewRoute($view, ...$args);
                }
                
                throw new \BadMethodCallException("Method {$method} does not exist");
            }
        };
    }
}