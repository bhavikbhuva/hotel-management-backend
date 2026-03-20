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
        return __('admin.create_new_blog');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make(__('admin.savedraft'))
                ->label(__('admin.save_draft'))
                ->color('gray')
                ->formId('form')
                ->action(function (): void {
                    $this->saveBlog(BlogStatus::Draft->value);
                }),
            Action::make(__('admin.publish'))
                ->label(__('admin.publish'))
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
                    ->title(__('admin.cannot_publish_this_blog'))
                    ->body(__('admin.the_selected_category_is_still_in_draft_publish_the_category_first'))
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
