<?php
namespace Jaravel\Helpers;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class Url
{
    /**
     * Generate a Joomla URL for a Laravel route
     *
     * @param string $component Component name (e.g., 'com_jaravelcomponent')
     * @param string $path Laravel route path
     * @param array $params Additional parameters
     * @return string
     */
    public static function route($component, $path = '', $params = [])
    {
        $url = 'index.php?option=' . $component;

        if ($path) {
            $url .= '&path=' . ltrim($path, '/');
        }

        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }

        // Find menu item ID
        $itemId = MenuHelper::findMenuItemId($component, $path);
        if ($itemId > 0) {
            $url .= '&Itemid=' . $itemId;
        }

        // Use Joomla's Route helper for SEF URLs
        return \Joomla\CMS\Router\Route::_($url);
    }

    /**
     * Generate an absolute URL
     */
    public static function absolute($component, $path = '', $params = [])
    {

        return \Joomla\CMS\Uri\Uri::root() . self::route($component, $path, $params);
    }

    /**
     * Create a component-specific URL helper
     *
     * @param string $component Component name
     * @return object
     */
    public static function component($component)
    {
        return new class($component) {
            private $component;

            public function __construct($component)
            {
                $this->component = $component;
            }

            public function route($path = '', $params = [])
            {
                return Url::route($this->component, $path, $params);
            }

            public function absolute($path = '', $params = [])
            {
                return Url::absolute($this->component, $path, $params);
            }
        };
    }
}