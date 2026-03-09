# Laravel 12 + Filament 5 — Quick Reference

## What We Installed

### 1. Laravel 12 (v12.53.0)
Laravel is a PHP web framework. We created a fresh project using:

```bash
composer create-project laravel/laravel .
```

This command:
- Scaffolded the entire Laravel project (routes, config, models, migrations, etc.)
- Created a `.env` file with app config (database, app key, etc.)
- Generated an `APP_KEY` (used for encryption)
- Created a SQLite database at `database/database.sqlite`
- Ran the default migrations (users, cache, jobs tables)

### 2. Filament 5 (v5.3.2)
Filament is an admin panel framework built on top of Laravel and Livewire. It gives you a full CRUD admin panel with almost zero code.

**Install command:**
```bash
composer require filament/filament:"^5.0"
```

**Setup command (creates the admin panel):**
```bash
php artisan filament:install --panels
```

This command:
- Created `app/Providers/Filament/AdminPanelProvider.php` — the main config for your admin panel
- Registered it in `bootstrap/providers.php`
- Published JS/CSS assets to `public/js/filament/` and `public/css/filament/`
- Published Inter font files to `public/fonts/filament/`

### 3. Create Admin User
```bash
php artisan make:filament-user
```
Prompts you for name, email, and password. This creates a user in the `users` table who can log in to the admin panel.

---

## Key Files to Know

| File | Purpose |
|------|---------|
| `app/Providers/Filament/AdminPanelProvider.php` | Main admin panel configuration (colors, pages, widgets, middleware) |
| `app/Models/User.php` | The User model (used for admin login) |
| `app/Filament/Resources/` | Where your CRUD resources go (auto-discovered) |
| `app/Filament/Pages/` | Custom admin pages (auto-discovered) |
| `app/Filament/Widgets/` | Dashboard widgets (auto-discovered) |
| `database/database.sqlite` | Your SQLite database |
| `database/migrations/` | Database migration files |
| `routes/web.php` | Web routes (Filament has its own routes via the panel provider) |
| `.env` | Environment config (DB, app key, mail, etc.) |
| `composer.json` | PHP dependencies |

---

## How Filament Works (Big Picture)

```
User visits /admin
       ↓
AdminPanelProvider.php defines the panel
       ↓
Filament handles login (via Laravel auth)
       ↓
Dashboard loads with widgets
       ↓
Resources (CRUD) appear in the sidebar automatically
```

### The Admin Panel Provider
`AdminPanelProvider.php` is the heart of your Filament setup. It configures:
- **`->path('admin')`** — the URL prefix (`/admin`)
- **`->login()`** — enables the login page
- **`->colors([...])`** — theme colors (currently Amber)
- **`->discoverResources(...)`** — auto-finds Resource classes in `app/Filament/Resources/`
- **`->discoverPages(...)`** — auto-finds Page classes in `app/Filament/Pages/`
- **`->discoverWidgets(...)`** — auto-finds Widget classes in `app/Filament/Widgets/`
- **`->middleware([...])`** — HTTP middleware stack
- **`->authMiddleware([...])`** — authentication middleware

### What is a Resource?
A Resource is Filament's way of creating a full CRUD interface for a model. One resource = one model = list/create/edit/view pages.

### What is Livewire?
Filament is built on Livewire — a Laravel package that lets you build reactive UIs without writing JavaScript. When you interact with a Filament form or table, Livewire handles the reactivity via AJAX calls behind the scenes.

---

## Common Commands

```bash
# Start the development server
php artisan serve

# Run migrations
php artisan migrate

# Create a new Filament resource (CRUD for a model)
php artisan make:filament-resource ModelName

# Create a new model with migration
php artisan make:model ModelName -m

# Create a new Filament page
php artisan make:filament-page PageName

# Create a new Filament widget
php artisan make:filament-widget WidgetName

# Create an admin user
php artisan make:filament-user

# Clear all caches
php artisan optimize:clear

# List all routes
php artisan route:list

# Run tests
php artisan test
```

---

## What's Running Right Now

- **URL:** `http://localhost:8000/admin` (when `php artisan serve` is running)
- **Database:** SQLite (`database/database.sqlite`)
- **Admin panel:** Filament 5 with Amber theme
- **Default pages:** Dashboard with Account widget and Filament Info widget
- **No resources yet** — the sidebar is empty (just the dashboard)

---

## Next Steps

To start building, you'll typically:
1. Create a model + migration (`php artisan make:model Post -m`)
2. Define the migration columns, run `php artisan migrate`
3. Create a Filament resource (`php artisan make:filament-resource Post`)
4. Customize the resource's form (fields), table (columns), and pages

Ready to build!
