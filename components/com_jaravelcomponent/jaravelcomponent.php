<?php
defined('_JEXEC') or die;

// Load Laravel autoloader from library and register component namespace
$loader = require JPATH_LIBRARIES . '/jaravel/vendor/autoload.php';
$loader->addPsr4('JaravelComponent\\', JPATH_COMPONENT . '/japp/app');

// Create Laravel app
$app = new Illuminate\Foundation\Application(
	JPATH_COMPONENT . '/japp'
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

// Handle request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
	$request = Illuminate\Http\Request::capture()
);

// Handle different response types
if ($response instanceof \Illuminate\Http\JsonResponse) {
	// For JSON responses, set headers and exit
	$response->send();
	$kernel->terminate($request, $response);
	jexit();
} elseif ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
	// For file downloads
	$response->send();
	$kernel->terminate($request, $response);
	jexit();
} else {
	// For normal HTML responses, output within Joomla
	echo $response->getContent();
	$kernel->terminate($request, $response);
}