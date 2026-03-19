<?php

namespace App\Filament\Pages;

use App\Models\WhoWeAre;
use App\Models\KeyHighlight;
use App\Models\OurPromise;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class AboutManage extends Page
{
    use WithFileUploads;

    protected static ?string $slug = 'manage-about';

    protected static ?string $title = 'About Page Management';

    protected static ?string $navigationLabel = 'About Page';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.about-manage';

    // ── Who We Are ────────────────────────────────────────────────────
    public ?string $who_title = null;
    public ?string $who_short_description = null;
    public ?string $who_content = null;
    public ?string $who_image = null;
    public $whoImageUpload = null;

    // ── Key Highlights ──────────────────────────────────────────────
    public array $highlights = [];

    // ── Our Promise ───────────────────────────────────────────────────
    public ?string $promise_title = null;
    public ?string $promise_content = null;
    public array $promise_features = [];

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
        return 'About Page Management';
    }

    public function getSubheading(): ?string
    {
        return "Control the text and information displayed in your property's about section.";
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
        $whoWeAre = WhoWeAre::first();
        $this->who_title = $whoWeAre?->title;
        $this->who_short_description = $whoWeAre?->short_description;
        $this->who_content = $whoWeAre?->content;
        $this->who_image = $whoWeAre?->image;

        $dbHighlights = KeyHighlight::orderBy('sort_order')->get();
        if ($dbHighlights->isEmpty()) {
            $this->highlights = [
                ['id' => null, 'title' => '', 'description' => '']
            ];
        } else {
            foreach ($dbHighlights as $hl) {
                $this->highlights[] = [
                    'id' => $hl->id,
                    'title' => $hl->title,
                    'description' => $hl->description,
                ];
            }
        }

        $ourPromise = OurPromise::first();
        $this->promise_title = $ourPromise?->title;
        $this->promise_content = $ourPromise?->content;
        
        $features = $ourPromise?->features ?? [];
        if (empty($features)) {
            $this->promise_features = ['']; 
        } else {
            $this->promise_features = $features;
        }
    }

    // ── Repeaters Add/Remove Methods ────────────────────────────────
    public function addHighlight(): void
    {
        if (count($this->highlights) < 4) {
            $this->highlights[] = ['id' => null, 'title' => '', 'description' => ''];
        }
    }

    public function removeHighlight(int $index): void
    {
        unset($this->highlights[$index]);
        $this->highlights = array_values($this->highlights);
    }

    public function addFeature(): void
    {
        if (count($this->promise_features) < 4) {
            $this->promise_features[] = '';
        }
    }

    public function removeFeature(int $index): void
    {
        unset($this->promise_features[$index]);
        $this->promise_features = array_values($this->promise_features);
    }

    // ── Who We Are Save ────────────────────────────────────────────────
    public function saveWhoWeAre(): void
    {
        $this->validate([
            'who_title' => ['required', 'string', 'max:255'],
            'who_short_description' => ['required', 'string', 'max:255'],
            'who_content' => ['required', 'string'],
            'whoImageUpload' => ['nullable', 'file', 'mimes:png,svg', 'max:2048'],
        ]);

        if ($this->whoImageUpload) {
            $this->who_image = $this->whoImageUpload->store('about', 'public');
            $this->whoImageUpload = null;
        }

        $whoWeAre = WhoWeAre::firstOrCreate([]);
        $whoWeAre->update([
            'title' => $this->who_title,
            'short_description' => $this->who_short_description,
            'content' => $this->who_content,
            'image' => $this->who_image,
        ]);

        Notification::make()
            ->title('Who We Are section saved successfully.')
            ->success()
            ->send();
    }

    // ── Key Highlights Save ──────────────────────────────────────────────
    public function saveKeyHighlights(): void
    {
        $this->validate([
            'highlights' => ['array', 'max:4'],
            'highlights.*.title' => ['required', 'string', 'max:255'],
            'highlights.*.description' => ['required', 'string'],
        ]);

        KeyHighlight::truncate();

        $order = 0;
        $insertData = [];
        foreach ($this->highlights as $item) {
            $insertData[] = [
                'title' => $item['title'],
                'description' => $item['description'],
                'sort_order' => $order++,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($insertData)) {
            KeyHighlight::insert($insertData);
        }

        Notification::make()
            ->title('Key Highlights saved successfully.')
            ->success()
            ->send();
    }

    // ── Our Promise Save ────────────────────────────────────────────────
    public function saveOurPromise(): void
    {
        $this->validate([
            'promise_title' => ['required', 'string', 'max:255'],
            'promise_content' => ['required', 'string'],
            'promise_features' => ['array', 'max:4'],
            'promise_features.*' => ['nullable', 'string', 'max:255'],
        ]);

        $cleanedFeatures = array_values(array_filter($this->promise_features, fn($v) => trim((string)$v) !== ''));

        $ourPromise = OurPromise::firstOrCreate([]);
        $ourPromise->update([
            'title' => $this->promise_title,
            'content' => $this->promise_content,
            'features' => $cleanedFeatures,
        ]);
        
        $this->promise_features = empty($cleanedFeatures) ? [''] : $cleanedFeatures;

        Notification::make()
            ->title('Our Promise section saved successfully.')
            ->success()
            ->send();
    }
}
