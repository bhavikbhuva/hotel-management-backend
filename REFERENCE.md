# Laravel 12 + Filament 5 — Quick Reference

## What We Installed

### 1. Laravel 12
PHP web framework. Fresh project created via:
```bash
composer create-project laravel/laravel .
```
Scaffolds the full Laravel directory structure, generates `APP_KEY`, creates `.env`, and runs default migrations.

### 2. Filament 5
Admin panel framework built on top of Laravel and Livewire.
```bash
composer require filament/filament:"^5.0"
php artisan filament:install --panels
```
Creates `app/Providers/Filament/AdminPanelProvider.php` — the main panel config file. Registered in `bootstrap/providers.php`.

### 3. spatie/laravel-permission (v7.x)
Role & permission management for Laravel.
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
Created `config/permission.php` and a migration for roles/permissions/pivot tables.

---

## Current State

- **URL:** `http://localhost:8000/` (when `php artisan serve` is running)
- **Database:** MySQL (`bookhub` via `.env`)
- **Admin panel:** Filament 5, Blue theme, branded as "BookingHub"
- **Admin is created via setup wizard** — `php artisan make:filament-user` is NOT used
- **Setup wizard:** `/setup` — shown on first visit. Stores all settings in the `settings` table

---

## Key Files

| File | Purpose |
|------|---------|
| `app/Providers/Filament/AdminPanelProvider.php` | Panel config: colors, login page, middleware, brand name |
| `app/Models/User.php` | User model — implements `FilamentUser`, uses `HasRoles`, `SoftDeletes` |
| `app/Filament/Pages/SetupWizard.php` | Multi-step setup wizard (pure Livewire component) |
| `app/Filament/Pages/Auth/Login.php` | Custom login page — overrides heading, tracks `last_login_at` |
| `app/Http/Middleware/EnsureSetupIsCompleted.php` | Redirects to `/setup` if setup not done |
| `app/Http/Middleware/FilamentAuthenticate.php` | Skips auth during setup; delegates to Filament auth after |
| `app/Models/Setting.php` | Key-value settings store with caching |
| `app/Models/Country.php` | Country model — name, iso_code, currency info, is_active |
| `app/Models/OperatingCountry.php` | Pivot model — countries the business operates in |
| `app/Models/PropertyType.php` | Property type model — is_active flag for selection |
| `app/Enums/UserRole.php` | Enum: `Admin`, `Partner`, `Staff`, `Customer` |
| `app/Enums/UserStatus.php` | Enum: `Active`, `Inactive`, `Suspended`, `Banned` |
| `app/Enums/SetupTask.php` | Enum: `AdminProfile`, `Cities`, `Taxes`, `CancellationPolicy`, `LegalPolicy` |
| `app/Models/CountrySetupTask.php` | Tracks setup task completion per country |
| `app/Filament/Pages/Dashboard.php` | Custom dashboard — shows checklist or actual dashboard |
| `app/Livewire/Topbar.php` | Custom topbar — country switcher, branch dropdown, notifications |
| `resources/views/livewire/topbar.blade.php` | Custom topbar Blade view |
| `resources/views/filament/pages/dashboard-checklist.blade.php` | Platform Setup Checklist view |
| `resources/views/livewire/setup-wizard.blade.php` | Full custom Blade view for the setup wizard |
| `resources/views/layouts/setup.blade.php` | Minimal HTML layout for the setup wizard (Vite + Livewire assets) |
| `routes/web.php` | Registers `/setup` route pointing to `SetupWizard::class` |
| `database/migrations/` | All migration files |
| `.env` | Environment config (DB, app key, etc.) |

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
Resources (CRUD) appear in sidebar automatically
```

### AdminPanelProvider.php configures:
- `->path('admin')` — URL prefix
- `->login(Login::class)` — custom login page
- `->colors([...])` — Blue theme
- `->discoverResources/Pages/Widgets(...)` — auto-discovery
- `->middleware([EnsureSetupIsCompleted::class, ...])` — runs before auth
- `->authMiddleware([FilamentAuthenticate::class])` — custom auth check

---

## Common Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Clear all caches (also clears settings cache)
php artisan cache:clear

# Create a Filament CRUD resource
php artisan make:filament-resource ModelName

# Create a model with migration
php artisan make:model ModelName -m

# Create a Filament page
php artisan make:filament-page PageName

# List all routes
php artisan route:list

# Run tests
php artisan test

# Build frontend assets (needed after adding new Blade files)
npm run build
# or for hot reload:
npm run dev
```

---

## Module 1: Authentication & Installation Wizard

### Settings Model

`app/Models/Setting.php` — a simple key-value store backed by the `settings` table.
```php
// Get a setting (cached forever, returns default if not set)
Setting::get('setup_completed');          // returns 'true' or null
Setting::get('some_key', 'fallback');

// Set a setting (upserts DB row, clears cache)
Setting::set('setup_completed', 'true');
```
Values are cached via `Cache::rememberForever("setting.{$key}")`. The cache is cleared on every `set()` call. If you manually delete a row from the DB, run `php artisan cache:clear` or the old cached value will still be returned.

### Users Table Schema

```
id, branch_id (nullable), name, avatar (nullable), email (unique),
phone (nullable), password (hashed), role (default: customer), status (default: active),
locale (default: en), auth_provider (nullable), country_id (nullable),
referral_code (nullable, unique), referred_by (nullable),
email_verified_at, phone_verified_at, last_login_at,
remember_token, created_at, updated_at, deleted_at (soft delete)
```

- `branch_id`, `country_id`, `referred_by` are plain `unsignedBigInteger` nullable columns — foreign key constraints added later when those tables exist.
- `password` is cast as `hashed` in the model — assign plaintext, Laravel hashes it automatically.

### How the Wizard Works (Full Flow)

```
Fresh install → visit any URL (e.g. /)
       ↓
EnsureSetupIsCompleted checks Setting::get('setup_completed')
       ↓ not 'true'
Redirect to /setup
       ↓
SetupWizard::mount() — if setup_completed is 'true', redirect back to /
       ↓
Step 1: Admin Account (centered card layout)
  - Fields: name, email, phone (optional), password, confirm password
  - Validates and advances to Step 2
       ↓
Step 2: System Mode (two-column sidebar layout)
  - Two selectable cards: Multi-Property Setup / Single-Property Setup
  - systemMode = 'single' (default) or 'multi'
  - Warning: Multi-Property cannot be changed later
       ↓
Step 3: Select Countries (multi-select)
  - 4-column grid of country cards with flag, name, currency info
  - Toggle selection: click to add/remove (multiple countries allowed)
  - Search bar with live debounce filtering by name or currency
  - Shows "X selected" count badge
  - Validation: at least 1 country required
       ↓
Step 4: Property Type (single-select + custom creation)
  - 4-column grid of property type cards with icon, name, checkbox
  - 5 pre-seeded defaults: Hotel, Homestay, Villa, Apartment, Resort
  - Single select: click to choose one type (admin decides what they operate)
  - "Add Property Type" card opens modal (icon upload, name, description)
  - Custom types appear in grid unselected after creation
  - Validation: exactly 1 type required
  - Footer shows "Finish Setup →" instead of "Next →"
       ↓
complete():
  - Creates User (role=admin, status=active, email_verified_at=now)
  - Setting::set('system_mode', 'single'|'multi')
  - Bulk inserts selected countries into `operating_countries` table
  - Updates `property_types.is_active` for the selected type
  - Sets `current_country_id` on user to first selected country
  - Seeds `country_setup_tasks` rows (1 global + 4 per country)
  - Setting::set('setup_completed', 'true')
  - Filament::auth()->login($user)
  - Redirect to /
       ↓
Dashboard checks if all 5 setup tasks are complete for current country
  - If incomplete → shows Platform Setup Checklist (5 cards + progress bar)
  - If complete → shows actual dashboard (widgets, charts, stats)
       ↓
User completes all 5 config pages → main dashboard unlocks for that country
```

After setup:
- `/setup` redirects immediately to `/`
- Login page shows "BookingHub Admin" branding
- Only `admin` and `staff` roles can access `/admin`

### SetupWizard Architecture (Important — Not a Filament Page)

`SetupWizard` **does NOT extend Filament's `SimplePage`**. It is a **pure Livewire component** (`extends Livewire\Component`).

Why: Filament's Wizard component only renders steps in a horizontal top bar. The design requires a full two-column layout (left sidebar navigation + right content). A pure Livewire component with a custom Blade view gives full layout control.

**`#[Layout('layouts.setup')]`** — tells Livewire to use `resources/views/layouts/setup.blade.php` as the HTML wrapper (loads Vite CSS/JS and Livewire scripts).

**Step tracking:**
- `$currentStep` — which step is active (starts at 1)
- `$totalSteps` — total number of steps (currently 4). When a new step is added, increment this and add its validation and Blade block.

**`nextStep()`** — validates the current step via `validateCurrentStep()`, then:
- If `$currentStep >= $totalSteps` → calls `complete()` (setup is done)
- Otherwise → increments `$currentStep`

**`validateCurrentStep()`** — uses `match($this->currentStep)` to apply the correct Livewire validation rules per step. Validation errors surface via `@error('field')` in the Blade view.

### Countries & Operating Countries

**`countries` table** — ~130 countries seeded via `CountrySeeder` (uses `updateOrCreate` on `iso_code` for idempotency).
```
id, name, iso_code (char 2, unique), currency_symbol (nullable),
currency_code (char 3), currency_name, is_active (default true), timestamps
```

**`operating_countries` table** — countries the business operates in. Populated during setup wizard Step 3.
```
id, country_id (FK → countries, unique, cascadeOnDelete), timestamps
```

**Why a dedicated table instead of settings JSON:**
The entire admin panel (cities, properties, branches) will be scoped by operating countries. A proper table enables Eloquent relationships, easy querying (`OperatingCountry::pluck('country_id')`), and foreign key constraints from future tables.

**Models:**
- `Country` — has `operatingCountry(): HasOne` relationship
- `OperatingCountry` — has `country(): BelongsTo` relationship, `$fillable = ['country_id']`

**Flag images:** `public/assets/flags/{iso_code}.svg` (274 SVG files, lowercase ISO 3166-1 alpha-2 codes)

### Property Types

**`property_types` table** — types of properties the platform supports (Hotel, Villa, etc.).
```
id, name (unique), description (text, nullable), icon (string, nullable),
is_default (boolean) — seeded types, is_active (boolean) — selected during setup, timestamps
```

**Single-select with `is_active` flag:**
Admin chooses one property type during setup (what they operate — Hotel, Villa, etc.). The selected type gets `is_active = true`, all others stay `false`. Uses `$set()` in Blade (not toggle) for single-select behavior.

**Model:** `PropertyType` — `$fillable`, casts `is_default` and `is_active` as boolean. No relationships yet.

**Default icons:** `public/assets/propertyTypes/{Name}.svg` (TitleCase: `Hotel.svg`, `Homestay.svg`, `Villa.svg`, `Apartment.svg`, `Resort.svg`)

**Custom type icons:** Uploaded via Livewire `WithFileUploads` to `public` disk under `propertyTypes/`. Stored at `storage/app/public/propertyTypes/`. Symlinked via `php artisan storage:link`.

**Rendering logic:**
- Default types (`is_default = true`): `<img src="/assets/propertyTypes/{{ $type->icon }}">`
- Custom types (`is_default = false`): `<img src="{{ Storage::url('propertyTypes/' . $type->icon) }}">`

**Modal for custom types:**
- `$showAddPropertyTypeModal` controls visibility
- Fields: icon upload (PNG/SVG, max 5MB), name (unique), description (required)
- `createPropertyType()` validates, stores icon, creates record with `is_default = false, is_active = false`
- New types appear in grid unselected (user must manually check them)

### Country Context & Setup Tasks

**`current_country_id` and `current_branch_id` on `users` table:**
Admin's active country/branch context. Persists across sessions (stored in DB, not session). When switching country, branch resets to null (branches are country-scoped).

**`country_setup_tasks` table** — tracks completion of 5 setup tasks per country:
```
id, country_id (FK → countries, nullable), task_key (string), completed_at (datetime, nullable), timestamps
unique(country_id, task_key)
```

**`SetupTask` enum** (`app/Enums/SetupTask.php`):
- `AdminProfile` — global (country_id = null), counts as done for all countries
- `Cities` — per-country
- `Taxes` — per-country
- `CancellationPolicy` — per-country
- `LegalPolicy` — per-country

Each enum case provides: `label()`, `description()`, `buttonLabel()`, `isGlobal()`, `countryScoped()`.

**`CountrySetupTask` model** (`app/Models/CountrySetupTask.php`):
- Scopes: `forCountry($id)` (includes global tasks), `completed()`, `incomplete()`
- Static helpers: `isCountryFullySetup($id)`, `markComplete($task, $countryId)`, `seedForCountries($ids)`
- Seeded during `SetupWizard::complete()` — not in a database seeder

**Dashboard unlock logic:** All 5 tasks (1 global + 4 country-specific) must be complete for the selected country. Each country is independent — completing Country A doesn't affect Country B.

### Custom Topbar

**`app/Livewire/Topbar.php`** replaces Filament's default topbar via `->topbarLivewireComponent()`.

Layout: `[← back] [Branch dropdown] ............ [Country dropdown] [🔔] [User menu]`

- **Back button**: `history.back()` via JS
- **Branch dropdown**: Hidden when 0 branches, static name when 1, dropdown when 2+. Branch = Property (used interchangeably).
- **Country dropdown**: Shows flag + name of current country. Lists all operating countries. Switching country calls `User::switchCountry()` which resets branch to null.
- **Notification bell**: Placeholder (icon only, no functionality yet)
- **User menu**: Reuses Filament's `HasUserMenu` trait

**Computed properties**: `$this->operatingCountries` (all operating countries), `$this->currentCountry` (active country model).

### Custom Dashboard

**`app/Filament/Pages/Dashboard.php`** extends `Filament\Pages\Dashboard`.

**Conditional rendering via `getView()`:**
- If setup incomplete for current country → renders `filament.pages.dashboard-checklist`
- If complete → renders default Filament page view (with widgets)

**Checklist view** (`resources/views/filament/pages/dashboard-checklist.blade.php`):
- Header: "Platform Setup Checklist" + progress bar (X of 5 tasks completed)
- 3-column grid of 5 cards (3 top row, 2 bottom row)
- Each card: icon, title, description, CTA button (links to config page)
- Completed cards: green checkmark state with "Completed" badge

### How to Add a New Wizard Step

1. **In `SetupWizard.php`:**
   - Add public properties for the step's data
   - Increment `$totalSteps`
   - Add validation rules in `validateCurrentStep()` via `match`
   - Use the values in `complete()`

2. **In `setup-wizard.blade.php`:**
   - Add to `$configSteps` array in the sidebar
   - Add a new `@if ($currentStep === N)` content block in the right panel area

### Layout Structure (Blade View)

`resources/views/livewire/setup-wizard.blade.php` has two conditional layouts:

**Step 1** — `@if ($currentStep === 1)`:
- Gray background, centered white card
- Logo, heading, form fields with inline SVG icons, "Next: System Mode →" button

**Steps 2+** — `@else`:
- Full-height two-column flex layout
- **Left sidebar** (224px fixed): logo placeholder → "Initial Setup" header → step list (each step has a circle indicator: blue filled = active, blue with checkmark = completed, gray border = future) → copyright footer
- **Right content**: scrollable area with `p-10` padding → step-specific content → fixed footer bar with Back / Next buttons

### Auth Middleware Details

Two custom middleware control access:

**`EnsureSetupIsCompleted`** (in `->middleware()` — runs on every panel request before auth):
- If `setup_completed !== 'true'` and path doesn't contain "setup" → redirect to `/setup`
- If done → pass through

**`FilamentAuthenticate`** (replaces default Filament `Authenticate` in `->authMiddleware()`):
- If `setup_completed !== 'true'` → skip auth entirely (lets unauthenticated users access setup)
- If done → delegate to Filament's standard auth, which calls `canAccessPanel()`

---
