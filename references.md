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
| `app/Models/Country.php` | Country model — name, iso_code, currency info, ref_country_id link |
| `app/Models/State.php` | State model — country relation, ref_state_id link, soft deletes |
| `app/Models/City.php` | City model — country+state relations, ref_city_id link, soft deletes |
| `app/Models/RefCountry.php` | Read-only reference country (from dr5hn SQL data) |
| `app/Models/RefState.php` | Read-only reference state (from dr5hn SQL data) |
| `app/Models/RefCity.php` | Read-only reference city (from dr5hn SQL data) |
| `app/Models/OperatingCountry.php` | Pivot model — countries the business operates in |
| `app/Models/PropertyType.php` | Property type model — is_active flag for selection |
| `app/Enums/UserRole.php` | Enum: `Admin`, `Partner`, `Staff`, `Customer` |
| `app/Enums/UserStatus.php` | Enum: `Active`, `Inactive`, `Suspended`, `Banned` |
| `app/Enums/SetupTask.php` | Enum: `AdminProfile`, `Cities`, `Taxes`, `CancellationPolicy`, `LegalPolicy` |
| `app/Models/CountrySetupTask.php` | Tracks setup task completion per country |
| `app/Filament/Pages/Dashboard.php` | Custom dashboard — shows checklist or actual dashboard |
| `app/Livewire/Topbar.php` | Custom topbar — country switcher, branch dropdown, notifications |
| `resources/views/livewire/topbar.blade.php` | Custom topbar Blade view |
| `app/Filament/Pages/AdminProfile.php` | Admin profile page — view profile, edit modal, change password |
| `resources/views/filament/pages/admin-profile.blade.php` | Admin profile Blade view — profile card, password form |
| `resources/views/filament/pages/dashboard-checklist.blade.php` | Platform Setup Checklist view |
| `resources/views/livewire/setup-wizard.blade.php` | Full custom Blade view for the setup wizard |
| `resources/views/layouts/setup.blade.php` | Minimal HTML layout for the setup wizard (Vite + Livewire assets) |
| `app/Filament/Pages/TaxManage.php` | Tax Management page — table + add/edit/delete modals |
| `resources/views/filament/pages/tax-manage.blade.php` | Tax Management Blade view |
| `app/Models/Tax.php` | Tax model — soft deletes, country/propertyType relations, forCountry scope |
| `app/Services/TaxService.php` | Tax business logic — create, update, delete |
| `app/Enums/TaxType.php` | Enum: `Percentage`, `Fixed` |
| `app/Enums/TaxStatus.php` | Enum: `Active`, `Inactive` |
| `app/Console/Commands/ImportRefDataCommand.php` | Imports dr5hn SQL files into ref_* tables via mysql CLI pipe |
| `app/Filament/Pages/CityManage.php` | City Management page — state/city selection per country |
| `app/Services/CityService.php` | City business logic — add/remove cities, state-as-city fallback |
| `resources/views/filament/pages/city-manage.blade.php` | City Management Blade view |
| `app/Filament/Resources/Blogs/BlogResource.php` | Blog resource — model, slug, navigation config, delegates to form/table/pages |
| `app/Filament/Resources/Blogs/Schemas/BlogForm.php` | Blog form schema — 3-column layout with live preview |
| `app/Filament/Resources/Blogs/Tables/BlogsTable.php` | Blog table — columns, filters, icon button actions, export |
| `app/Filament/Resources/Blogs/Pages/ListBlogs.php` | Blog list page — tabbed view (Blogs + Categories) |
| `app/Filament/Resources/Blogs/Pages/CreateBlog.php` | Blog create page — dual Save Draft / Publish actions |
| `app/Filament/Resources/Blogs/Pages/EditBlog.php` | Blog edit page — dual Save Draft / Publish + Delete actions |
| `app/Livewire/BlogCategoryTable.php` | Blog category table — standalone Livewire TableComponent with CRUD modals |
| `app/Models/Blog.php` | Blog model — SoftDeletes, category/author relations, published scope |
| `app/Models/BlogCategory.php` | Blog category model — SoftDeletes, blogs relation |
| `app/Enums/BlogStatus.php` | Enum: Draft, Published |
| `app/Enums/BlogCategoryStatus.php` | Enum: Draft, Published |
| `app/Services/BlogService.php` | Blog business logic — create, update, delete |
| `app/Services/BlogCategoryService.php` | Blog category business logic — create, update, delete |
| `resources/views/filament/pages/blogs/list-blogs.blade.php` | Blog list Blade — tab switcher with inline blue styles |
| `resources/views/filament/pages/blogs/view-blog.blade.php` | Blog view modal content — cover image, meta, content, SEO |
| `resources/views/filament/schemas/components/blog-preview.blade.php` | Live preview card — Alpine.js + FilePond events |
| `resources/views/livewire/blog-category-table.blade.php` | Category table Blade — empty state with modal trigger |
| `resources/css/filament/admin/theme.css` | Custom CSS — icon button styling for table record actions |
| `app/Filament/Pages/BannerManage.php` | Banner Management page — table + modal CRUD, country-scoped |
| `app/Models/Banner.php` | Banner model — SoftDeletes, country relation, forCountry scope |
| `app/Enums/BannerStatus.php` | Enum: Active, Inactive |
| `app/Services/BannerService.php` | Banner business logic — create, update, delete |
| `resources/views/filament/pages/banner-manage.blade.php` | Banner Management Blade view |
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

### Two-Tier Location Data Architecture

The app uses a **two-tier table pattern** for location data (countries, states, cities):

**Reference tables** (`ref_countries`, `ref_states`, `ref_cities`):
- Read-only data pool with ~250 countries, 5000+ states, 150000+ cities
- Sourced from [dr5hn/countries-states-cities-database](https://github.com/dr5hn/countries-states-cities-database)
- SQL files stored at `database/sql/countries.sql`, `database/sql/states.sql`, `database/sql/cities.sql`
- SQL files were modified: table names prefixed with `ref_`, FK constraints to non-existent regions/subregions tables removed
- Imported via `php artisan app:import-ref-data` — uses mysql CLI pipe (NOT `file_get_contents` or `DB::unprepared` — cities.sql is ~111MB)
- Import order: countries → states → cities (hardcoded in command)
- Models (`RefCountry`, `RefState`, `RefCity`) use `$guarded = ['*']` — never written to by the app

**Operational tables** (`countries`, `states`, `cities`):
- Lean app-specific tables holding only what the business uses
- Linked to ref tables via `ref_*_id` columns (nullable, unique, **no FK constraint** — ref tables exist outside migration lifecycle)
- Managed by Laravel migrations with proper relationships and soft deletes (states, cities)

**Why:** Replaced Google Places API with static SQL data to avoid API costs and external dependency.

### Countries & Operating Countries

**`countries` table** — operational countries created during setup wizard Step 3 from RefCountry data.
```
id, ref_country_id (nullable, unique — links to ref_countries.id, no FK),
name, iso_code (char 2, unique), currency_symbol (nullable),
currency_code (char 3), currency_name, is_active (default true), timestamps
```

**`operating_countries` table** — countries the business operates in. Populated during setup wizard Step 3.
```
id, country_id (FK → countries, unique, cascadeOnDelete), timestamps
```

**Why a dedicated table instead of settings JSON:**
The entire admin panel (cities, properties, branches) will be scoped by operating countries. A proper table enables Eloquent relationships, easy querying (`OperatingCountry::pluck('country_id')`), and foreign key constraints from future tables.

**Models:**
- `Country` — has `operatingCountry(): HasOne`, `states(): HasMany`, `cities(): HasMany`, `refCountry(): BelongsTo` relationships
- `OperatingCountry` — has `country(): BelongsTo` relationship, `$fillable = ['country_id']`

**Flag images:** `public/assets/flags/{iso_code}.svg` (274 SVG files, lowercase ISO 3166-1 alpha-2 codes)

### States

**`states` table** — operational states for countries the business uses.
```
id, ref_state_id (nullable, unique — links to ref_states.id, no FK),
country_id (FK → countries, cascadeOnDelete), name,
latitude (nullable), longitude (nullable), is_active (default true),
timestamps, deleted_at (soft delete)
```

**Model:** `State` — `country(): BelongsTo`, `cities(): HasMany`, `refState(): BelongsTo`. Uses `SoftDeletes`.

### Cities & City Management

**`cities` table** — operational cities for states the business uses.
```
id, ref_city_id (nullable, unique — links to ref_cities.id, no FK),
country_id (FK → countries, cascadeOnDelete),
state_id (FK → states, cascadeOnDelete), name,
latitude (nullable), longitude (nullable),
status (default 'active'), timestamps, deleted_at (soft delete)
```

**Model:** `City` — `country(): BelongsTo`, `state(): BelongsTo`, `refCity(): BelongsTo`. Uses `SoftDeletes`.

**Route:** `/cities` — 2nd setup task in the dashboard checklist.

**Page:** `CityManage` — state/city selection scoped to admin's `current_country_id`.

**State-as-city fallback:** For small countries/states with no city subdivisions (e.g., Seychelles), the city select offers the state itself with value `state:{ref_state_id}`. `CityService` handles this prefix pattern by creating a city record named after the state.

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
- **User menu**: Custom trigger (avatar + name + role) with Filament's `<x-filament::dropdown>` for content (profile, theme switcher, logout). Uses `HasUserMenu` trait. Menu items configured as `Action` objects in `AdminPanelProvider` (not deprecated `MenuItem`) to avoid `toAction()` override bugs.

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

### Tax Management

**Route:** `/taxes` — 3rd setup task in the dashboard checklist.

**Page:** `TaxManage` uses `InteractsWithTable` trait on a Filament Page (not a Resource). Taxes are scoped to the admin's `current_country_id`.

**Table columns:** Tax Name (with ID description), Description (limit 40), Type (badge: blue=Percentage, orange=Fixed), Value (formatted with % or currency symbol), Status (badge: green=Active, gray=Inactive).

**Header action:** "+ Add New Tax" opens modal with: Tax Name, Description, Calculation Type (Percentage/Fixed with live select), Rate Value (dynamic suffix: % or currency symbol), Status (radio: Active/Inactive, default Active).

**Table actions:** Edit (pencil icon, pre-filled modal) and Delete (trash icon, confirmation dialog with soft delete).

**Filters:** Status (All/Active/Inactive). **Search:** By tax name. **Pagination:** 4 per page.

**Service layer:** `TaxService` handles all business logic:
- `createTax()` — auto-sets `property_type_id` to active property type, marks `SetupTask::Taxes` complete on first tax for a country
- `updateTax()` — updates tax record
- `deleteTax()` — soft deletes

**Modal conventions (project-wide):**
- Footer alignment: `Alignment::End` (right-aligned)
- Button order: Cancel first, then Save (`order-first` on cancel action)
- Delete confirmation: icon, heading, description, "Yes, Delete" danger button

### Auth Middleware Details

Two custom middleware control access:

**`EnsureSetupIsCompleted`** (in `->middleware()` — runs on every panel request before auth):
- If `setup_completed !== 'true'` and path doesn't contain "setup" → redirect to `/setup`
- If done → pass through

**`FilamentAuthenticate`** (replaces default Filament `Authenticate` in `->authMiddleware()`):
- If `setup_completed !== 'true'` → skip auth entirely (lets unauthenticated users access setup)
- If done → delegate to Filament's standard auth, which calls `canAccessPanel()`

### Blog Module (Content Management)

**Navigation:** "Content Management" group in sidebar. Route: `/blogs`.

**Architecture:** Filament Resource (`BlogResource`) with separate Create/Edit pages (not modals). Blog categories use a standalone Livewire `TableComponent` embedded in a tab.

#### Blog Categories

**`blog_categories` table:**
```
id, name, slug (unique), status (default 'draft'), timestamps, deleted_at (soft delete)
```

**Model:** `BlogCategory` — `blogs(): HasMany`, uses `SoftDeletes`. Status cast to `BlogCategoryStatus` enum.

**UI:** Standalone `BlogCategoryTable` Livewire component (extends `TableComponent`). Rendered on the "Categories" tab of the blog list page.

**CRUD:** All via modals (create, edit, delete). Individual icon button actions (edit + delete), no dropdown.

**Edge cases:**
- Cannot set category to draft if it has published blogs
- Cannot delete category if it has blogs associated
- Slug auto-generates from name with collision handling (-2, -3, etc.)
- Slug uniqueness validated with custom error message

#### Blogs

**`blogs` table:**
```
id, blog_category_id (FK → blog_categories), created_by (nullable FK → users),
title, slug (unique), short_description (text), content (longText),
cover_image, meta_title, meta_description (text), keywords (text),
status (default 'draft'), published_at (nullable), timestamps, deleted_at (soft delete)
```

**Model:** `Blog` — `category(): BelongsTo BlogCategory`, `author(): BelongsTo User`, `published` scope. Status cast to `BlogStatus` enum.

**Service layer:** `BlogService` handles:
- `createBlog()` — auto-sets `created_by` via `auth()->id()`, `published_at` on publish
- `updateBlog()` — smart `published_at` handling (set on first publish, clear on draft, preserve if already published)
- `deleteBlog()` — soft delete

#### Blog List Page (Tabbed)

**Page:** `ListBlogs` — custom Blade view with two tabs:
- **All Blogs** tab — renders `BlogsTable` (Filament Resource table)
- **Categories** tab — renders `@livewire('blog-category-table')`

**Tab state:** `#[Url(as: 'tab')]` property for query string binding. Default: `blogs`.

**Tab styling:** Active tab uses inline `background-color: #2563eb` (blue) with white text, container `background-color: #dbeafe` (light blue). Tailwind dynamic classes don't compile — inline styles required.

**Breadcrumbs:** Removed via `getBreadcrumbs()` returning empty array.

#### Blog Create/Edit Pages

**Dual actions pattern:** Header has "Save Draft" (gray) + "Publish" (primary) buttons. Footer actions removed (`getFormActions()` returns `[]`).

**On Create:** Both actions use `->formId('form')` for form binding.

**On Edit:** Also includes `DeleteAction` in header.

**Business rules before save:**
- Validates form via `$this->form->validate()`
- Blocks publishing if selected category is still draft
- Redirects to index after save

#### Blog Form Schema (3-Column Layout)

**Column 1-2 (span 2):** Blog Configuration section:
- Title — `live(onBlur: true)`, auto-generates slug with collision handling
- Slug — unique validation, custom error message
- Category — searchable select, `live(onBlur: true)`, "Manage Categories" suffix action
- Short Description — textarea, `live(onBlur: true)`, max 500 chars
- Content — RichEditor with full toolbar (bold, italic, underline, strike, link, textColor, highlight, h1-h3, lead, small, alignments, blockquote, codeBlock, lists, horizontalRule, table, grid, details, attachFiles, floating table toolbar)
- Cover Image — FileUpload, public disk, max 2MB, JPG/PNG

**Column 3 (span 1):** Live Website Preview section:
- `View::make('filament.schemas.components.blog-preview')`
- Uses `$get()` for reactive field values
- Alpine.js `x-data` with `previewUrl` reactive property
- FilePond native `addfile` event at document level → `URL.createObjectURL()` for instant image preview
- FilePond `removefile` event clears back to stored image URL

**Below (span 2):** SEO Configuration section:
- Meta Title, Meta Description, Keywords (comma separated)

#### Blog Table

**Columns:** SR.NO, Image (ImageColumn, public disk), Blog Title, Blog Category (via relationship), Created Date, Status (badge).

**Filters:** Status select, Category relationship.

**Record actions:** Individual icon buttons (view modal, edit page link, delete with confirmation) — all `->iconButton()->color('gray')`.

**Toolbar:** Export + Create New Blog button.

#### Icon Button Styling (CSS)

Custom CSS in `resources/css/filament/admin/theme.css`:
```css
td .fi-ta-actions .fi-icon-btn {
    background-color: rgb(243 244 246) !important;
    border-radius: 0.625rem !important;
    width: 2.25rem !important;
    height: 2.25rem !important;
    margin-right: 4px !important;
}
```
Dark mode variant uses `rgb(55 65 81)`. Requires `npm run build` after changes.

### Banner Module (Marketing)

**Navigation:** Marketing > Banner & Advertisement. Route: `/banners`.

**Architecture:** Filament Page with `InteractsWithTable` (like TaxManage). Modal-based CRUD, country-scoped.

#### Banners

**`banners` table:**
```
id, country_id (nullable FK → countries), is_global (boolean, default false — future),
platform (varchar, default 'both' — future), title, image, target_url,
start_date (date, nullable), end_date (date, nullable),
status (default 'active'), sort_order (int, default 0 — future),
timestamps, deleted_at (soft delete)
```

**Model:** `Banner` — `country(): BelongsTo`, `forCountry` scope, uses `SoftDeletes`. Status cast to `BannerStatus` enum.

**Service:** `BannerService` — `createBanner()` auto-sets `country_id` from auth user's current_country, `updateBanner()`, `deleteBanner()` (soft).

**Scheduling logic:**
- No dates → banner displays immediately if active
- Start date only → visible from start date until manually deactivated
- Start + end date → visible only within date range
- Table shows "Scheduled" label when start date is in the future

**Create/Edit modal fields:**
- Selected Country (read-only Placeholder with flag + name + ISO)
- Start Date / End Date (optional DatePickers, side by side)
- Banner Title (required)
- Upload Banner (FileUpload, public disk, max 2MB, 1872×750, JPG/PNG)
- External Target Link (required, URL validated)
- Status (Radio: Active/Inactive, inline, default Active)

**Table columns:** Banner image + title, Banner Dates (formatted with scheduling logic), Target Link (clickable, truncated), Status (badge), Actions (edit + delete icon buttons).

**Future fields (in DB, not in UI):** `is_global` (global vs country-specific), `platform` (app/web/both), `sort_order` (display ordering).

---
