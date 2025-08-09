<?php

namespace Jaravel;

use Joomla\CMS\Component\Router\RouterBase;

/**
 * Base router class for Jaravel components
 * 
 * Provides reusable build and parse methods for SEF URL handling.
 * Components can extend this class to get automatic SEF URL support
 * without duplicating code.
 */
class Router extends RouterBase
{
    /**
     * Build the route for the component (Joomla to SEF)
     * 
     * @param array $query The query parameters to build into a route
     * @return array The URL segments
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

        // Handle any additional parameters
        // Subclasses can override this method to handle component-specific parameters
        $this->buildAdditionalSegments($segments, $query);

        return $segments;
    }

    /**
     * Parse the segments of a URL (SEF to Joomla)
     * 
     * @param array $segments The URL segments to parse
     * @return array The query variables
     */
    public function parse(&$segments)
    {
        $vars = [];

        // Allow subclasses to handle special segments first
        $this->parseSpecialSegments($vars, $segments);

        // Combine all segments back into a path
        if (!empty($segments)) {
            $vars['path'] = implode('/', $segments);

            // Clear segments as we've processed them
            $segments = [];
        }

        return $vars;
    }

    /**
     * Build additional segments for component-specific parameters
     * 
     * Override this method in component routers to handle custom parameters.
     * 
     * @param array $segments The segments array to add to
     * @param array $query The query parameters (passed by reference)
     */
    protected function buildAdditionalSegments(&$segments, &$query)
    {
        // Default implementation does nothing
        // Subclasses can override to add component-specific logic
    }

    /**
     * Parse special segments before standard path processing
     * 
     * Override this method in component routers to handle special URL patterns.
     * 
     * @param array $vars The variables array to add to
     * @param array $segments The segments array (passed by reference)
     */
    protected function parseSpecialSegments(&$vars, &$segments)
    {
        // Default implementation does nothing
        // Subclasses can override to add component-specific logic
    }
}