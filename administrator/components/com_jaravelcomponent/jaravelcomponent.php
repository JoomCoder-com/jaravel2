<?php
defined('_JEXEC') or die;

// Import Jaravel library
JLoader::import('jaravel', JPATH_LIBRARIES);

// Bootstrap and run Laravel app with custom namespace for admin
$jaravel = new \Jaravel\Bootstrap(
    JPATH_COMPONENT . '/japp',
    'JaravelComponentAdmin'  // Different namespace for admin
);
$jaravel->run();