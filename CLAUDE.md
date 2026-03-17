<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.17
- filament/filament (FILAMENT) - v5
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.
- use services and actions and keep controllers thin, always follow good code practice.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

=== architecture rules ===

# Architecture

- Controllers must remain thin. Controllers should only:
  - receive request
  - call service classes
  - return response

Services may call multiple Actions.
Actions must never call Services.

- Business logic must never be placed in:
  - Controllers
  - Filament Resources
  - Livewire components

- Business logic must live in:
  - Services
  - Actions

Structure:

Controller
   → Service
      → Action

Example:

CreateBookingController
   → BookingService
      → CreateBookingAction
      → LockInventoryAction
      → CalculateTaxesAction

- Each Action must have a single public method:
handle()

- Action naming format:
Verb + Entity + Action

Examples:
CreateBookingAction
CancelBookingAction
LockInventoryAction
CalculateCommissionAction

=== filament rules ===

# Filament Admin Panel Rules

- Filament Resources must not contain business logic.

- Filament Resources should only:
  - define forms
  - define tables
  - call services

Example:

Instead of:

Property::create($data)

Use:

app(PropertyService::class)->createProperty($data);

- Do not place database logic inside Filament actions.

## Table Action Buttons Convention (CRITICAL — FOLLOW FOR ALL TABLES)

Table record actions must use individual icon buttons — NEVER use `ActionGroup` (three-dot dropdown) for record actions.

```php
->recordActions([
    Action::make('edit')
        ->iconButton()
        ->icon('heroicon-o-pencil')
        ->color('gray'),
    Action::make('delete')
        ->iconButton()
        ->icon('heroicon-o-trash')
        ->color('gray')
        ->requiresConfirmation(),
])
```

### Rules:
- Always use `->iconButton()` with `->color('gray')` for record actions
- Icon buttons get gray background + rounded corners via CSS in `theme.css`
- Never wrap record actions in `ActionGroup::make([])`
- Standard actions: view (heroicon-o-eye), edit (heroicon-o-pencil), delete (heroicon-o-trash)
- CSS for icon button styling is in `resources/css/filament/admin/theme.css`

## Dual Save Actions Convention (Save Draft / Publish)

For pages with draft/publish workflow, override header actions and remove form footer actions:

```php
protected function getHeaderActions(): array
{
    return [
        Action::make('saveDraft')->label('Save Draft')->color('gray')->action(fn () => ...),
        Action::make('publish')->label('Publish')->action(fn () => ...),
    ];
}

protected function getFormActions(): array
{
    return []; // Remove default footer buttons
}
```

### Rules:
- On Create pages, add `->formId('form')` to header actions
- Always validate form, check business rules (e.g., category draft status), then call service
- Redirect to index after save: `$this->redirect(Resource::getUrl('index'))`

## Live Preview Convention

For live form previews (e.g., blog card preview), use `Filament\Schemas\Components\View` with a Blade component:

```php
View::make('filament.schemas.components.blog-preview')
```

### Rules:
- Use `$get('field_name')` in Blade to access reactive form values
- Fields feeding the preview MUST have `->live(onBlur: true)` for text or `->live()` for selects/uploads
- For file upload preview: use Alpine.js with document-level `FilePond:addfile` event listener and `URL.createObjectURL()` for instant client-side preview
- Preview Blade component goes in `resources/views/filament/schemas/components/`

## Tab-Based List Pages

For pages with multiple tabs (e.g., Blogs + Categories), use a custom Blade view with tab switcher:

### Rules:
- Use `#[Url(as: 'tab')]` property for query string binding
- Active tab styling: inline `background-color: #2563eb` (blue) with white text, container `background-color: #dbeafe` (light blue)
- Tailwind classes like `bg-primary-600` may not compile — use inline styles for dynamic tab colors
- Tab content conditionally rendered in Blade using `$this->activeTab`
- Remove breadcrumbs with: `public function getBreadcrumbs(): array { return []; }`

## Table Export Convention (CRITICAL — FOLLOW FOR ALL TABLES)

Every table MUST include an export button using the reusable `TableExportAction`.

- Package: `openspout/openspout` (lightweight, streaming CSV/XLSX writer)
- Service: `App\Services\ExportService` — handles file generation (never duplicate this logic)
- Action: `App\Filament\Actions\TableExportAction` — reusable Filament action for any table
- Supports: CSV and Excel (XLSX) via user-selected dropdown in a modal
- No queue required — files download instantly (safe for up to ~5,000 rows)

### How to add export to a new table:

```php
use App\Filament\Actions\TableExportAction;

->toolbarActions([
    TableExportAction::make()
        ->filename('my-table-name')
        ->exports([
            'attribute' => 'Header Label',
            'relation.attribute' => 'Related Header',
            'computed' => ['label' => 'Custom', 'formatter' => fn ($record) => $record->someMethod()],
        ])
        ->toActionGroup(),
])
```

### Rules:
- ALWAYS add `TableExportAction` to `toolbarActions()` on every new table
- Use simple string mapping for direct attributes, array with `formatter` for computed values
- Enum/status columns MUST use a formatter to export the human-readable label, not the raw value
- Filename should match the table's entity name (e.g., 'cities', 'taxes', 'bookings')
- If switching to queue-based export (Filament ExportAction) later, see memory for migration guide

=== project structure rules ===

# Application Folder Structure

Follow this structure:

app/
  Actions/
  Services/
  Enums/
  Models/
  Http/
    Controllers/
    Requests/
  Filament/
    Actions/
    Pages/
  Policies/

Do not create new top-level directories without approval.

=== booking rules ===

# Booking Engine Rules

- Inventory must always be validated before creating a booking.

- Inventory locking must be used before payment.

Flow:

Check availability
→ Create inventory lock
→ Create pending booking
→ Process payment
→ Confirm booking

- Inventory locks must expire automatically using expires_at.

=== deletion rules ===

# Deletion Rules

All entities must use soft deletes.

Never use hard deletes unless explicitly approved.

Tables using soft deletes:
users
partners
properties
branches
room_types
bookings
blog_categories
blogs
banners

=== enum rules ===

# Enums

Statuses must use PHP Enums instead of raw strings.

Examples:

UserStatus
BookingStatus
PartnerStatus
PropertyStatus

When generating migrations, always include indexes for foreign keys.


=== testing rules ===

Testing should focus only on critical business logic.

Write tests only for:
- booking creation
- inventory locking
- cancellation logic
- refund logic
- financial calculations

Do not generate tests for simple CRUD or Filament resources unless requested.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

# must follow
I will give you exact details each time, you will never assume anything, and always ask me if there is a query anywhere. also dont just code it, give me detailed explaination of each thing everytime

## Framework-First Rule (CRITICAL — NEVER VIOLATE)
- ALWAYS use Filament/Livewire/Laravel built-in components before writing custom HTML, JS, or Blade.
- NEVER replace a framework component with raw HTML/JS. If you need to customize, extend or wrap the existing component — do not rewrite it from scratch.
- Before writing ANY UI element (form input, dropdown, modal, toggle, menu), search the framework docs (`search-docs`) to check if a built-in component exists. If it does, use it.
- When customizing a framework component (e.g., custom topbar), you MUST preserve ALL features the original component provided (theme switcher, accessibility, dark mode support, etc.). List what the original provides before replacing it.
- If a built-in component is missing a feature you need, ask the user before deciding to go custom.

## No Assumptions Rule (CRITICAL — NEVER VIOLATE)
- NEVER assume an approach, architecture decision, component choice, or implementation detail without asking the user first.
- Before every implementation step, explain what you plan to do and why, then wait for confirmation.
- If there are multiple ways to do something, present the options with trade-offs and let the user decide.
- This includes: FK constraints, column types, component choices, file structure, migration strategies, and any decision that affects the codebase.

## Memory & Documentation Rule
- After completing any significant work (new architecture, new patterns, important decisions), save relevant memories immediately — do not wait to be asked.
- Keep project memories, reference memories, and feedback memories up to date throughout the conversation.
- If external resources, repos, or tools were used, save them as reference memories.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
