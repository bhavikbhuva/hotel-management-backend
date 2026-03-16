<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Enums\BlogCategoryStatus;
use App\Enums\BlogStatus;
use App\Filament\Resources\Blogs\BlogResource;
use App\Models\BlogCategory;
use App\Services\BlogService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'Create New Blog';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveDraft')
                ->label('Save Draft')
                ->color('gray')
                ->formId('form')
                ->action(function (): void {
                    $this->saveBlog(BlogStatus::Draft->value);
                }),
            Action::make('publish')
                ->label('Publish')
                ->formId('form')
                ->action(function (): void {
                    $this->saveBlog(BlogStatus::Published->value);
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function saveBlog(string $status): void
    {
        $this->form->validate();
        $data = $this->form->getState();

        if ($status === BlogStatus::Published->value) {
            $category = BlogCategory::find($data['blog_category_id']);

            if ($category && $category->status === BlogCategoryStatus::Draft) {
                Notification::make()
                    ->title('Cannot publish this blog.')
                    ->body('The selected category is still in draft. Publish the category first.')
                    ->danger()
                    ->send();

                return;
            }
        }

        $blog = app(BlogService::class)->createBlog($data, $status);

        Notification::make()
            ->title($status === BlogStatus::Published->value ? 'Blog published successfully.' : 'Blog saved as draft.')
            ->success()
            ->send();

        $this->redirect(BlogResource::getUrl('index'));
    }
}
