<?php

namespace App\Filament\Pages;

use App\Filament\Enums\NavigationGroup;

use App\Enums\FacilityStatus;
use App\Models\Facility;
use App\Models\HomepageAboutUs;
use App\Models\HomepageAmenity;
use App\Models\Review;
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

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('admin.manage_homepage');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.manage_homepage');
    }

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
        return NavigationGroup::ContentManagement;
    }

    public function getHeading(): string|Htmlable
    {
        return __('admin.manage_homepage_sections');
    }

    public function getSubheading(): ?string
    {
        return __('admin.customize_your_property_homepage_sections_with_realtime_preview');
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
        $aboutUs = HomepageAboutUs::first();
        $this->about_title = $aboutUs?->title;
        $this->about_description = $aboutUs?->description;
        $this->about_button_text = $aboutUs?->button_text;
        $this->about_contact_no = $aboutUs?->contact_no;
        $this->about_image = $aboutUs?->image;

        // Amenities
        $amenities = HomepageAmenity::orderBy('sort_order')->get();
        $this->amenities_selected = $amenities->pluck('facility_id')->toArray();
        $this->amenities_descriptions = $amenities->mapWithKeys(fn ($item) => [(int) $item->facility_id => $item->description ?? ''])->toArray();

        // Guest Reviews
        $featuredReviews = Review::where('is_featured', true)->orderBy('featured_order')->get();
        $this->reviews_selected = $featuredReviews->pluck('id')->toArray();
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

        $aboutUs = HomepageAboutUs::firstOrCreate([]);
        $aboutUs->update([
            'title' => $this->about_title,
            'description' => $this->about_description,
            'button_text' => $this->about_button_text,
            'contact_no' => $this->about_contact_no,
            'image' => $this->about_image,
        ]);

        Notification::make()
            ->title(__('admin.about_us_section_saved_successfully'))
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

        // Clear existing
        HomepageAmenity::truncate();

        // Insert new ones in order
        $insertData = [];
        $order = 0;
        foreach ($this->amenities_selected as $facilityId) {
            $insertData[] = [
                'facility_id' => (int) $facilityId,
                'description' => $this->amenities_descriptions[(int) $facilityId] ?? '',
                'sort_order' => $order++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        HomepageAmenity::insert($insertData);

        Notification::make()
            ->title(__('admin.amenities_facilities_saved_successfully'))
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

        // Reset all
        Review::where('is_featured', true)->update([
            'is_featured' => false,
            'featured_order' => null
        ]);

        // Update selected
        if (!empty($this->reviews_selected)) {
            foreach (array_values(array_map('intval', $this->reviews_selected)) as $index => $reviewId) {
                Review::where('id', $reviewId)->update([
                    'is_featured' => true,
                    'featured_order' => $index
                ]);
            }
        }

        Notification::make()
            ->title(__('admin.guest_reviews_saved_successfully'))
            ->success()
            ->send();
    }

    public function getAvailableReviewsProperty(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        return Review::query()
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
        
        return Review::query()
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
