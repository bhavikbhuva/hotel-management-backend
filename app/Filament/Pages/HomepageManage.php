<?php

namespace App\Filament\Pages;

use App\Enums\FacilityStatus;
use App\Models\Facility;
use App\Services\HomepageSectionService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class HomepageManage extends Page
{
    use WithFileUploads;

    protected static ?string $slug = 'manage-homepage';

    protected static ?string $title = 'Manage Homepage';

    protected static ?string $navigationLabel = 'Manage Homepage';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.homepage-manage';

    // ── About Us ────────────────────────────────────────────────────
    public ?string $about_title = null;

    public ?string $about_description = null;

    public ?string $about_button_text = null;

    public ?string $about_contact_no = null;

    /** @var string|null Stored path (persisted in DB) */
    public ?string $about_image = null;

    /** @var TemporaryUploadedFile|null Pending upload (not yet saved) */
    public $aboutImageUpload = null;

    // ── Amenities & Facilities ──────────────────────────────────────
    /** @var array<int> Selected facility IDs */
    public array $amenities_selected = [];

    /** @var array<int|string, string> Descriptions keyed by facility_id */
    public array $amenities_descriptions = [];

    // ── Guest Reviews ───────────────────────────────────────────────
    /** @var array<int> Selected review IDs */
    public array $reviews_selected = [];

    public string $reviewSearch = '';
    public ?int $reviewStarsFilter = null;

    // ────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Content Management';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Manage Homepage Sections';
    }

    public function getSubheading(): ?string
    {
        return 'Customize your property homepage sections with real-time preview.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(): void
    {
        // About Us
        $aboutSection = app(HomepageSectionService::class)->getSection('about_us');
        $this->about_title = $aboutSection?->title;
        $this->about_description = $aboutSection?->description;
        $this->about_button_text = $aboutSection?->button_text;
        $this->about_contact_no = $aboutSection?->contact_no;
        $this->about_image = $aboutSection?->image;

        // Amenities
        $amenitiesSection = app(HomepageSectionService::class)->getSection('amenities');
        $amenitiesData = $amenitiesSection?->amenities_data ?? [];

        $this->amenities_selected = collect($amenitiesData)
            ->pluck('facility_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $this->amenities_descriptions = collect($amenitiesData)
            ->mapWithKeys(fn ($item) => [(int) $item['facility_id'] => $item['description'] ?? ''])
            ->toArray();

        // Guest Reviews
        $reviewsSection = app(HomepageSectionService::class)->getSection('guest_reviews');
        $this->reviews_selected = collect($reviewsSection?->reviews_data ?? [])
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    // ── About Us Save ────────────────────────────────────────────────
    public function saveAboutUs(): void
    {
        $this->validate([
            'about_title' => ['required', 'string', 'max:255'],
            'about_description' => ['required', 'string'],
            'about_button_text' => ['required', 'string', 'max:100'],
            'about_contact_no' => ['required', 'string', 'max:20'],
            'aboutImageUpload' => ['nullable', 'file', 'mimes:png,svg', 'max:2048'],
        ]);

        if ($this->aboutImageUpload) {
            $this->about_image = $this->aboutImageUpload->store('homepage', 'public');
            $this->aboutImageUpload = null;
        }

        app(HomepageSectionService::class)->updateSection('about_us', [
            'title' => $this->about_title,
            'description' => $this->about_description,
            'button_text' => $this->about_button_text,
            'contact_no' => $this->about_contact_no,
            'image' => $this->about_image,
        ]);

        Notification::make()
            ->title('About Us section saved successfully.')
            ->success()
            ->send();
    }

    // ── Amenities Save ───────────────────────────────────────────────
    public function saveAmenities(): void
    {
        $this->validate([
            'amenities_selected' => ['required', 'array', 'min:1'],
            'amenities_descriptions.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $amenitiesData = collect($this->amenities_selected)
            ->map(fn ($id) => [
                'facility_id' => (int) $id,
                'description' => $this->amenities_descriptions[(int) $id] ?? '',
            ])
            ->values()
            ->toArray();

        app(HomepageSectionService::class)->updateSection('amenities', [
            'amenities_data' => $amenitiesData,
        ]);

        Notification::make()
            ->title('Amenities & Facilities saved successfully.')
            ->success()
            ->send();
    }

    // ── Guest Reviews Save ───────────────────────────────────────────
    public function saveReviews(): void
    {
        $this->validate([
            'reviews_selected' => ['nullable', 'array'],
            'reviews_selected.*' => ['integer', 'exists:reviews,id'],
        ]);

        app(HomepageSectionService::class)->updateSection('guest_reviews', [
            'reviews_data' => array_values(array_map('intval', $this->reviews_selected)),
        ]);

        Notification::make()
            ->title('Guest Reviews saved successfully.')
            ->success()
            ->send();
    }

    public function getAvailableReviewsProperty(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        return \App\Models\Review::query()
            ->with('user')
            ->where('status', 'approved')
            ->when($this->reviewSearch, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('review', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($this->reviewStarsFilter, function ($query, $stars) {
                if ($stars == 5) {
                    $query->where('rating', 5);
                } else {
                    $query->where('rating', '>=', $stars)
                          ->where('rating', '<', $stars + 1);
                }
            })
            ->latest()
            ->limit(20)
            ->get();
    }

    public function getSelectedReviewsModelsProperty(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        if (empty($this->reviews_selected)) {
            return collect();
        }
        
        return \App\Models\Review::query()
            ->with('user')
            ->whereIn('id', $this->reviews_selected)
            ->get()
            ->sortBy(fn($r) => array_search($r->id, $this->reviews_selected))
            ->values();
    }

    // ── View Data ────────────────────────────────────────────────────
    protected function getViewData(): array
    {
        return [
            'facilities' => Facility::query()
                ->with('category')
                ->where('status', FacilityStatus::Active)
                ->orderBy('sort_order')
                ->get(),
        ];
    }

    /**
     * Return facilities keyed by ID for easy lookup in the Blade view.
     *
     * @return Collection<int, Facility>
     */
    public function getFacilitiesMapProperty(): Collection
    {
        return $this->getViewData()['facilities']->keyBy('id');
    }
}
