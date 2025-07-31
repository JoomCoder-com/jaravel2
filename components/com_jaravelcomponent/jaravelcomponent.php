<?php
defined('_JEXEC') or die;

use Illuminate\Http\JsonResponse;
use Joomla\CMS\Factory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Load Laravel autoloader from library and register component namespace
$loader = require JPATH_LIBRARIES . '/jaravel/vendor/autoload.php'; // laravel 12 + jaravel helper classes


$loader->addPsr4('JaravelComponent\\', __DIR__ . '/japp/app');

// Create Laravel app
$app = new Illuminate\Foundation\Application(
	__DIR__ . '/japp'
);

// Register services with custom namespace
$app->singleton(
	Illuminate\Contracts\Http\Kernel::class,
	JaravelComponent\Http\Kernel::class
);

$app->singleton(
	Illuminate\Contracts\Console\Kernel::class,
	JaravelComponent\Console\Kernel::class
);

$app->singleton(
	Illuminate\Contracts\Debug\ExceptionHandler::class,
	JaravelComponent\Exceptions\Handler::class
);

// Get the path from Joomla input
$jinput = Joomla\CMS\Factory::getApplication()->input;

// Check for path parameter first
$path = $jinput->get('path', '', 'STRING');

// If no path, check for view parameter and map it
if (!$path) {
    $view = $jinput->get('view', '', 'CMD');
    
    // View to Laravel path mappings
    $viewMappings = [
        'home' => '',           // home view maps to root path
        'tasks' => 'tasks',     // tasks view maps to tasks path
        'dashboard' => 'dashboard',
        'search' => 'search',
        'admin' => 'admin/stats'
    ];
    
    // Get path from view mapping or use view name as path
    $path = isset($viewMappings[$view]) ? $viewMappings[$view] : $view;
}

// Default to root if still no path
$path = $path ?: '/';

// Create a custom request with the correct path
$server = $_SERVER;
$server['REQUEST_URI'] = '/' . ltrim($path, '/');
$server['PATH_INFO'] = '/' . ltrim($path, '/');

// Create request with modified server variables
$request = Illuminate\Http\Request::create(
	'/' . ltrim($path, '/'),
	$_SERVER['REQUEST_METHOD'] ?? 'GET',
	$_REQUEST,
	$_COOKIE,
	$_FILES,
	$server
);

// Handle request with custom request object
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);


ob_start();
$response = $kernel->handle($request);
ob_end_clean(); // Discard buffer

// Simple check: Is it HTML or not?
$contentType = $response->headers->get('Content-Type', 'text/html');
$isHtml = stripos($contentType, 'text/html') !== false;

if ($isHtml) {
	// HTML: Output within Joomla
	echo $response->getContent();
	$kernel->terminate($request, $response);
} else {
	// Everything else: Send directly and exit
	$response->send();
	$kernel->terminate($request, $response);
	jexit();
}