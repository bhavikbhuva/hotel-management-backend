<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Filament\Resources\Blogs\BlogResource;
use App\Models\Blog;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

class ListBlogs extends ListRecords
{
    protected static string $resource = BlogResource::class;

    protected string $view = 'filament.pages.blogs.list-blogs';

    #[Url(as: 'tab')]
    public string $currentTab = 'blogs';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getHeading(): string|Htmlable
    {
        return 'Blogs Management';
    }

    public function getSubheading(): ?string
    {
        return 'Create, edit, and manage your content marketing articles.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getHasBlogs(): bool
    {
        return Blog::query()->exists();
    }

    public function switchTab(string $tab): void
    {
        $this->currentTab = $tab;
    }
}
