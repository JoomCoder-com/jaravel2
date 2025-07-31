<?php

namespace Jaravel\Helpers;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;

class MenuHelper
{
    /**
     * Find menu item ID based on Laravel route path
     *
     * @param string $component Component name
     * @param string $path Laravel route path
     * @param string|null $language Language tag
     * @return int Menu item ID or 0 if not found
     */
    public static function findMenuItemId($component, $path = '', $language = null)
    {
        // Get all menu items
        $menus = Factory::getContainer()->get(SiteApplication::class)->getMenu()->getMenu();
        
        // Get current language if not specified
        if ($language === null) {
            $language = Factory::getLanguage()->getTag();
        }
        
        // Clean the path
        $path = trim($path, '/');
        
        // Priority rules for finding menu items
        $rules = [];
        
        // First priority: exact path match
        if ($path) {
            $rules[] = ['component' => $component, 'path' => $path];
        }
        
        // Second priority: parent paths (for nested routes)
        if ($path && strpos($path, '/') !== false) {
            $segments = explode('/', $path);
            while (count($segments) > 1) {
                array_pop($segments);
                $rules[] = ['component' => $component, 'path' => implode('/', $segments)];
            }
        }
        
        // Third priority: component home page
        $rules[] = ['component' => $component, 'path' => ''];
        
        // Execute rules in order
        foreach ($rules as $rule) {
            $itemId = self::executeRule($rule, $menus, $language);
            if ($itemId > 0) {
                return $itemId;
            }
        }
        
        // Fallback to active menu item if it's the same component
        $activeMenu = Factory::getApplication()->getMenu()->getActive();
        if ($activeMenu && $activeMenu->component == $component) {
            return $activeMenu->id;
        }
        
        // Last resort: find any menu item for this component
        foreach ($menus as $menu) {
            if ($menu->component == $component) {
                // Check language compatibility
                if (self::checkLanguage($menu, $language)) {
                    return $menu->id;
                }
            }
        }
        
        return 0;
    }
    
    /**
     * Execute a single rule to find menu item
     *
     * @param array $rule Rule to execute
     * @param array $menus Array of menu items
     * @param string $language Language tag
     * @return int Menu item ID or 0 if not found
     */
    private static function executeRule($rule, $menus, $language)
    {
        foreach ($menus as $menu) {
            // Check component match
            if ($menu->component != $rule['component']) {
                continue;
            }
            
            // Check language compatibility
            if (!self::checkLanguage($menu, $language)) {
                continue;
            }
            
            // Check path match
            $menuPath = isset($menu->query['path']) ? $menu->query['path'] : '';
            
            // Exact match
            if ($menuPath == $rule['path']) {
                return $menu->id;
            }
            
            // For component home, accept menu items without path
            if ($rule['path'] === '' && $menuPath === '') {
                return $menu->id;
            }
        }
        
        return 0;
    }
    
    /**
     * Check if menu item language is compatible
     *
     * @param object $menu Menu item
     * @param string $language Language tag
     * @return bool
     */
    private static function checkLanguage($menu, $language)
    {
        // If multilanguage is not enabled, always return true
        if (!Multilanguage::isEnabled()) {
            return true;
        }
        
        // If menu language is *, it's available for all languages
        if ($menu->language == '*') {
            return true;
        }
        
        // Check exact language match
        return $menu->language == $language;
    }
    
    /**
     * Get the currently active menu item ID for the component
     *
     * @param string $component Component name
     * @return int|null
     */
    public static function getActiveMenuItemId($component)
    {
        $activeMenu = Factory::getApplication()->getMenu()->getActive();
        
        if ($activeMenu && $activeMenu->component == $component) {
            return $activeMenu->id;
        }
        
        return null;
    }
}