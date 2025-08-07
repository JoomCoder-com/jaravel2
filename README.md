# Jaravel3 - Laravel Integration Framework for Joomla

A powerful framework that enables multiple isolated Laravel 12 applications to run as Joomla 5.x components, combining the robustness of Joomla CMS with the modern development experience of Laravel.

## Features

- **Multiple Laravel Apps in One Joomla Installation** - Run multiple isolated Laravel applications as Joomla components
- **Shared Laravel Framework** - Single Laravel 12 library serving all components (efficient resource usage)
- **Complete Isolation** - Each component has its own routes, controllers, models, views, and configuration
- **Livewire Support** - Build reactive UI components with Laravel Livewire
- **Blade Templating** - Use Laravel's powerful Blade templating within Joomla
- **Seamless Integration** - Laravel apps render within Joomla's template system
- **Joomla Authentication** - Access Joomla's user system from Laravel apps

## Requirements

- PHP 8.2 or higher
- Joomla 5.2 or higher
- MySQL/MariaDB or PostgreSQL
- Composer

## Installation

1. Clone this repository:
```bash
git clone https://github.com/yourusername/jaravel3.git
cd jaravel3
```

2. Install Joomla following standard procedures

3. Install the Laravel framework dependencies:
```bash
cd libraries/jaravel
composer install
```

4. Install the example component dependencies:
```bash
cd components/com_jaravelcomponent/japp
composer install
```

## Architecture

```
jaravel3/
├── libraries/
│   └── jaravel/              # Shared Laravel 12 framework
│       ├── src/
│       ├── vendor/
│       └── composer.json
├── components/
│   └── com_jaravelcomponent/ # Example component
│       ├── japp/             # Isolated Laravel application
│       │   ├── app/          # Controllers, Models, Livewire
│       │   ├── config/       # Laravel configuration
│       │   ├── resources/    # Views and assets
│       │   ├── routes/       # Laravel routes
│       │   └── composer.json
│       └── jaravelcomponent.php # Bridge between Joomla and Laravel
└── [Standard Joomla structure]
```

## Creating a New Jaravel Component

1. **Copy the example structure**:
```bash
cp -r components/com_jaravelcomponent components/com_yourcomponent
```

2. **Rename the bridge file**:
```bash
mv components/com_yourcomponent/jaravelcomponent.php components/com_yourcomponent/yourcomponent.php
```

3. **Update the bridge file** using the JaravelBootstrap class:
```php
<?php
defined('_JEXEC') or die;

use Jaravel\JaravelBootstrap;

// Define your view to route mappings
$viewMappings = [
    'home' => '',              // Joomla view 'home' -> Laravel route '/'
    'products' => 'products',   // Joomla view 'products' -> Laravel route '/products'
    'contact' => 'contact',     // Joomla view 'contact' -> Laravel route '/contact'
];

// Bootstrap your Laravel application
JaravelBootstrap::boot('yourcomponent', __DIR__, $viewMappings);
```

4. **Install your component's dependencies**:
```bash
cd components/com_yourcomponent/japp
composer install
```

5. **Develop your Laravel application** in the `japp/` directory using standard Laravel practices

## Usage Example

### Creating a Controller

In `components/com_yourcomponent/japp/app/Http/Controllers/ProductController.php`:
```php
namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }
}
```

### Creating a Blade View

In `components/com_yourcomponent/japp/resources/views/products/index.blade.php`:
```blade
@extends('layouts.app')

@section('content')
    <h1>Products</h1>
    @foreach($products as $product)
        <div>
            <h2>{{ $product->name }}</h2>
            <a href="{{ $jurl('products.show', ['id' => $product->id]) }}">View Details</a>
        </div>
    @endforeach
@endsection
```

### Using Livewire Components

```bash
cd components/com_yourcomponent/japp
php artisan make:livewire ProductSearch
```

## Key Concepts

### JaravelBootstrap Class
The framework provides a `JaravelBootstrap` class that handles all the boilerplate code for integrating Laravel with Joomla:

```php
// Simple usage
JaravelBootstrap::boot('componentname', __DIR__, $viewMappings);

// Advanced usage with custom configuration
$bootstrap = new JaravelBootstrap('componentname', __DIR__, $viewMappings);
$app = $bootstrap->getApp();
// Add custom service providers, middleware, etc.
$app->register(CustomServiceProvider::class);
$bootstrap->handle();

// Manual request handling for special cases
$bootstrap = new JaravelBootstrap('componentname', __DIR__);
$request = Illuminate\Http\Request::create('/custom-path', 'POST', ['data' => 'value']);
$bootstrap->handle($request);
```

### Route Mapping
The JaravelBootstrap class automatically maps Joomla view parameters to Laravel routes through the view mappings array, allowing seamless URL routing through Joomla's menu system.

### Response Handling
- **HTML responses** render within Joomla's template
- **JSON/API responses** bypass Joomla for direct output
- **File downloads** handled directly by Laravel

### Component Isolation
Each component maintains:
- Independent Laravel application instance
- Separate namespace (`App\`)
- Own database tables
- Isolated storage directory
- Independent composer dependencies

## Development

### Running Commands

Update the shared Laravel framework:
```bash
cd libraries/jaravel && composer update
```

Update a specific component:
```bash
cd components/com_yourcomponent/japp && composer update
```

### Database Migrations

Run migrations for a specific component:
```bash
cd components/com_yourcomponent/japp
php artisan migrate
```

## Benefits

- **Best of Both Worlds** - Joomla's CMS capabilities with Laravel's modern development
- **Rapid Development** - Use Laravel's artisan commands, migrations, and eloquent ORM
- **Scalable Architecture** - Add new components without affecting existing ones
- **Resource Efficient** - Single Laravel framework serves multiple applications
- **Modern Frontend** - Support for Livewire, Vue.js, React, or any frontend framework

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source software licensed under the [MIT license](LICENSE).

## Support

- Create an issue on GitHub for bug reports or feature requests
- Documentation available in [CLAUDE.md](CLAUDE.md) for AI-assisted development

## Credits

Built on top of:
- [Joomla! CMS](https://www.joomla.org/) - Open Source Content Management System
- [Laravel Framework](https://laravel.com/) - The PHP Framework for Web Artisans

## Development Tools

This project was developed with the help of:
- [Claude Code](https://claude.ai/code) - AI-powered coding assistant
- [PHPStorm](https://www.jetbrains.com/phpstorm/) - Professional PHP IDE
- [GitHub](https://github.com/) - Version control and collaboration platform
- [Phing](https://www.phing.info/) - PHP build automation tool