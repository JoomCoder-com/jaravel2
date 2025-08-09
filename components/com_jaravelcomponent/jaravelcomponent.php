<?php
defined('_JEXEC') or die;

// Load Laravel autoloader and Jaravel Bootstrap
require_once JPATH_LIBRARIES . '/jaravel/vendor/autoload.php';

use Jaravel\Bootstrap;

/**
 * Jaravel Component Entry Point
 * 
 * This component uses automatic view detection and mapping:
 * 
 * 1. Automatic Detection: The Jaravel library automatically detects views 
 *    in the /views folder and maps them to Laravel routes with matching names
 * 
 * 2. Default Mappings:
 *    - 'home' view → '/' (root route)
 *    - 'default' view → '/' (root route)
 *    - Other views map to routes with the same name (e.g., 'tasks' → '/tasks')
 * 
 * 3. Custom Overrides: Add entries to $customMappings to override automatic behavior
 */

// Initialize the component
$bootstrap = new Bootstrap(__DIR__, 'JaravelComponent');

// Custom view-to-route mappings (optional - overrides automatic detection)
// Uncomment and modify as needed:
$customMappings = [
    // 'admin' => 'admin/stats',        // Override: admin view → admin/stats route
    // 'profile' => 'user/profile',     // Override: profile view → user/profile route
];

// Handle the request with automatic view detection and optional custom mappings
$bootstrap->handleRequest($customMappings);