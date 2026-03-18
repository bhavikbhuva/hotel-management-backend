# BookingHub Admin — Quick Reference

## Stack

- **Laravel 12** — PHP framework
- **Filament 5** — admin panel (on Livewire 4)
- **spatie/laravel-permission v7** — roles & permissions
- **Database:** MySQL (`bookhub`)
- **Panel config:** `AdminPanelProvider.php` — Blue theme, branded "BookingHub"

---

## Architecture & Conventions

### Service Layer Pattern
All business logic lives in Services (never in controllers, pages, or resources).
```
Filament Page / Resource → Service → Model
```

### Country Scoping
Admin has `current_country_id` on users table (persists in DB, not session). Switching country resets branch to null. Most modules (taxes, banners, cities) are scoped to this; some are global (blogs, how-it-works, FAQs).

### Two-Tier Location Data
- **Reference tables** (`ref_countries`, `ref_states`, `ref_cities`) — read-only pool (~150k cities) from [dr5hn SQL data](https://github.com/dr5hn/countries-states-cities-database). Imported via `php artisan app:import-ref-data` (mysql CLI pipe for large files).
- **Operational tables** (`countries`, `states`, `cities`) — lean app-specific tables linked via `ref_*_id` (no FK constraint — ref tables exist outside migration lifecycle).
- **Why:** Replaced Google Places API to avoid costs and external dependency.

### Tabbed Pages Pattern
Used in Blogs and Help & Support. Tab state bound to URL via `#[Url(as: 'tab')]`. Active tab styled with inline `background-color: #2563eb` (Tailwind dynamic classes don't compile for this). Breadcrumbs removed.

### Modal Conventions
- Sticky header + footer (`->stickyModalHeader()`, `->stickyModalFooter()`)
- Footer: right-aligned, Cancel first then Save (`order-first` on cancel)
- Delete: confirmation with icon, heading, description, "Yes, Delete" danger button

### Table Conventions
- Record actions: individual icon buttons (`->iconButton()->color('gray')`), never ActionGroup
- Every table has `TableExportAction` in toolbar
- Empty state: conditional render — hide `{{ $this->table }}` entirely when no data, show custom empty div + `<x-filament-actions::modals />`

### Sort Order Pattern
Used across steps, topics, FAQs. Auto-assign on create (`max + 1`), re-sequence on delete to avoid gaps.

### Custom CSS (`theme.css`)
Layout restructure (fixed sidebar), icon button styling, hover-reveal actions (`.step-card`, `.topic-card`, `.faq-item`), modal scrollbar management, pagination layout, toolbar reordering.

---

## Modules

### Setup Wizard (`/setup`)
Pure Livewire component (NOT Filament page) — needed full two-column layout control. 4 steps: Admin Account → System Mode (single/multi property) → Select Countries → Property Type. Creates admin user, seeds `operating_countries` and `country_setup_tasks`. After setup, `/setup` redirects to `/`.

**Key decision:** System mode (single vs multi-property) cannot be changed after setup.

### Dashboard (`/`)
Conditional: shows Platform Setup Checklist (5-task progress bar + cards) until all tasks complete for current country, then shows actual dashboard. Each country is independent — completing one doesn't unlock others.

**5 Setup Tasks:** AdminProfile (global), Cities, Taxes, CancellationPolicy, LegalPolicy (per-country).

### Custom Topbar
Replaces Filament default via `->topbarLivewireComponent()`. Layout: Back button | Branch dropdown | ... | Country switcher | Notifications | User menu. Branch dropdown hides with 0 branches, shows static name with 1, dropdown with 2+.

### Admin Profile (`/admin-profile`)
View profile card + edit modal + change password section. Marks `SetupTask::AdminProfile` complete (global task).

### City Management (`/cities`)
Country-scoped. State/city selection from ref data. **State-as-city fallback:** for regions with no city subdivisions, the state itself can be selected as a city (value prefix `state:{ref_state_id}`).

### Tax Management (`/taxes`)
Country-scoped. Table with modal CRUD. Auto-sets `property_type_id` to active type. Marks `SetupTask::Taxes` complete on first tax for a country. Tax value displays with dynamic suffix (% or currency symbol based on type).

### Blog Module (`/blogs`)
Filament Resource with separate Create/Edit pages (not modals). Tabbed list: Blogs tab + Categories tab (standalone Livewire `TableComponent`).

**Business rules:**
- Cannot publish blog if its category is still draft
- Cannot set category to draft if it has published blogs
- Cannot delete category if it has associated blogs
- Slug auto-generates with collision handling (-2, -3, etc.)
- Dual header actions: Save Draft (gray) + Publish (primary), no footer actions
- `published_at` smart handling: set on first publish, clear on draft, preserve if re-saving published
- 3-column form: content (span 2) + live preview card (span 1) + SEO section below
- Live preview uses Alpine.js + FilePond native events for instant image preview

### Banner Module (`/banners`)
Country-scoped. Table with modal CRUD (like taxes).

**Scheduling logic:** No dates = immediate display. Start date only = visible from that date. Start + end = date range. Create modal enforces `minDate(now())` for dates; edit allows existing past dates.

**Future fields (in DB, not UI):** `is_global`, `platform` (app/web/both), `sort_order`.

### Help & Support FAQs (`/help-support`)
Global (not country-scoped). Two tabs:

**Tab 1 — How It Works:** Card-based layout (not a table). 4-column grid, max 4 steps enforced (UI hides button + backend validates). Edit/delete on hover. Auto sort_order with re-sequencing.

**Tab 2 — Topics & FAQs:** Accordion layout. Topics as collapsible sections (Alpine.js `x-collapse`) with nested FAQs. Topic has auto-generated slug with collision handling. FAQs scoped per topic with independent sort_order. Hover edit/delete on both topics and FAQs. "+ Add FAQ" link per topic.

### Manage Homepage (`/manage-homepage`)
Content Management group. Accordion-style page with predefined sections; each section row expands to show an inline form.

**Pattern:** `HasForms` + named form per section (e.g. `aboutUsForm`). Data is stored across explicit normalized tables. Pre-filled on `mount()` direct from models.

**Sections (Phase 1):**
- **About Us** — Section Title, Description, Button Text, Contact No., Image (PNG/SVG, 770×600px max 2MB). Stored in `homepage_about_us` table.
- **Amenities & Facilities** — Searchable Alpine.js multiselect with descriptions per selected facility. Saved as relational rows in `homepage_amenities` table.
- **Guest Reviews** — 2-column grid showing saved validation reviews. Features an 'Add Reviews' Dashed button that opens a native `<x-filament::modal>` containing an Alpine.js/Livewire multi-select grid with text search and star rating filter. Saved by updating `is_featured` flag and `featured_order` integer right on the `reviews` table records.

---

## Key Files

| File | Purpose |
|------|---------|
| `AdminPanelProvider.php` | Panel config — colors, login, middleware, brand |
| `app/Filament/Pages/SetupWizard.php` | 4-step setup wizard (pure Livewire) |
| `app/Filament/Pages/Dashboard.php` | Conditional checklist / actual dashboard |
| `app/Livewire/Topbar.php` | Custom topbar — country/branch switcher |
| `app/Filament/Pages/AdminProfile.php` | Profile view + edit modal |
| `app/Filament/Pages/CityManage.php` | City selection per country |
| `app/Filament/Pages/TaxManage.php` | Tax CRUD table, country-scoped |
| `app/Filament/Resources/Blogs/` | Blog resource — form, table, create/edit pages |
| `app/Livewire/BlogCategoryTable.php` | Blog categories — standalone table component |
| `app/Filament/Pages/BannerManage.php` | Banner CRUD table, country-scoped |
| `app/Filament/Pages/HelpSupportManage.php` | How It Works + Topics & FAQs (tabbed) |
| `app/Filament/Pages/HomepageManage.php` | Manage Homepage Sections — accordion page with inline forms |
| `app/Models/HomepageSection.php` | Singleton-style model per section key |
| `app/Services/HomepageSectionService.php` | `updateSection()` upsert, `getSection()` getter |
| `app/Services/` | All business logic services |
| `app/Enums/` | Status enums, roles, setup tasks |
| `resources/css/filament/admin/theme.css` | Custom CSS overrides |
| `database.md` | Full database schema reference |

---

## How to Add a New Wizard Step

1. In `SetupWizard.php`: add properties, increment `$totalSteps`, add validation in `validateCurrentStep()` match, use values in `complete()`
2. In `setup-wizard.blade.php`: add to `$configSteps` sidebar array, add `@if ($currentStep === N)` content block

## Common Commands

```bash
php artisan serve                    # Dev server
php artisan migrate                  # Run migrations
php artisan cache:clear              # Clear all caches (including settings)
php artisan app:import-ref-data      # Import dr5hn location SQL data
npm run build                        # Build assets (needed after Blade/CSS changes)
php artisan test --compact           # Run tests
vendor/bin/pint --dirty --format agent  # Format changed PHP files
```
