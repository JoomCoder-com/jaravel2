<?php
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase;

class JaravelcomponentRouter extends RouterBase
{
    /**
     * Build the route for the component (Joomla to SEF)
     */
    public function build(&$query)
    {
        $segments = [];

        // Handle the path parameter
        if (isset($query['path'])) {
            // Split the path into segments
            $pathSegments = explode('/', trim($query['path'], '/'));
            foreach ($pathSegments as $segment) {
                if ($segment !== '') {
                    $segments[] = $segment;
                }
            }
            unset($query['path']);
        }


		unset($query['view']);

        return $segments;
    }

    /**
     * Parse the segments of a URL (SEF to Joomla)
     */
    public function parse(&$segments)
    {
        $vars = [];

        // Combine all segments back into a path
        if (!empty($segments)) {
            $vars['path'] = implode('/', $segments);

            // Clear segments as we've processed them
            $segments = [];
        }

        return $vars;
    }
}