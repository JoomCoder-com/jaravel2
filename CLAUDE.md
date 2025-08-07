# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Jaravel3** is a Joomla 5.2 framework that enables multiple isolated Laravel 12 applications to run as Joomla components. Each component gets its own isolated Laravel instance while sharing a single Laravel framework library. The project requires PHP 8.2+ and supports Livewire for reactive UI.

## Architecture

### Core Structure
- **Joomla CMS** handles initial routing, authentication, and CMS features
- **Shared Laravel Library** (`/libraries/jaravel/`) - Single Laravel 12 framework used by all components
- **Multiple Laravel Apps** - Each Joomla component can have its own isolated Laravel application
- **Bridge Pattern** - Each component has a bridge file (e.g., `jaravelcomponent.php`) connecting Joomla to its Laravel instance

### Key Directories
- `/libraries/jaravel/` - Shared Laravel 12 framework library (used by all components)
- `/components/com_jaravelcomponent/` - Example component implementation
  - `japp/` - Isolated Laravel application for this component
    - `app/` - Controllers, Models, Livewire components
    - `resources/views/` - Blade templates
    - `routes/web.php` - Laravel routes
    - `config/` - Laravel configuration
  - `jaravelcomponent.php` - Bridge between Joomla and this Laravel instance

### Creating New Components
Each new Jaravel component follows the same pattern:
- `/components/com_[name]/` - Component directory
- `/components/com_[name]/japp/` - Isolated Laravel app
- `/components/com_[name]/[name].php` - Bridge file

## Development Commands

### Composer Dependencies
```bash
# Update shared Laravel framework (affects all components)
cd libraries/jaravel && composer update

# Update specific component's dependencies
cd components/com_[componentname]/japp && composer update
```

### Database
- Database: `jaravel_3`
- Table prefix: `xxnf2_`
- Configuration: `/configuration.php`

## Routing System

Each component's bridge file maps Joomla view parameters to Laravel routes:
```php
// In components/com_[name]/[name].php
$routes = [
    'default' => 'welcome',
    'helloworld' => 'helloworld',
    'about' => 'about',
    // Component-specific route mappings
];
```

## Component Isolation

### Each Component Has:
- **Isolated Laravel application** in `japp/` directory
- **Separate namespace** - Each component's Laravel app uses its own `App\` namespace
- **Independent routing** - Routes defined in component's `routes/web.php`
- **Own configuration** - Separate `config/` directory per component
- **Isolated storage** - Each component has its own `storage/` directory

### Shared Resources:
- **Laravel Framework** - Single `/libraries/jaravel/` installation
- **Joomla Authentication** - Components can access Joomla user system
- **Database Connection** - Shared database with component-specific tables

## Blade Templates

### URL Generation
Each component uses the custom `$jurl()` helper in Blade templates:
```blade
<a href="{{ $jurl('route.name', ['param' => 'value']) }}">Link</a>
```

### Livewire Components
Each component can have its own Livewire components:
- Location: `/components/com_[name]/japp/app/Livewire/`
- Templates: `/components/com_[name]/japp/resources/views/livewire/`

## Important Considerations

### When Creating New Components
1. Copy the example structure from `com_jaravelcomponent`
2. Update the bridge file with component-specific route mappings
3. Configure the component's Laravel app in `japp/config/`
4. Each component maintains its own composer dependencies

### Response Handling
- HTML responses render within Joomla template
- JSON/CSV responses bypass Joomla and send directly
- Each component's bridge file handles response type detection

### Namespace Management
- Shared library uses `Jaravel\` namespace
- Each component's Laravel app has isolated `App\` namespace
- No namespace conflicts between components

## Development Environment

- Platform: Windows (WAMP)
- Working directory: `C:\wamp64\www\jaravel3`
- Debug mode: Enabled in both frameworks
- No automated testing or linting configured