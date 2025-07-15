<?php

use Illuminate\Support\Facades\Route;
// Home route
Route::get('/', function () {
	return '<h1>Jaravel Home</h1>
        <ul>
            <li><a href="'.\Joomla\CMS\Router\Route::_('index.php?option=com_jaravelcomponent&path=about&Itemid=103').'">About (HTML)</a></li>
        </ul>';
});

// About page (HTML response)
Route::get('/about', function () {
	return '<h1>About Jaravel</h1><p>Laravel running inside Joomla!</p>';
});
