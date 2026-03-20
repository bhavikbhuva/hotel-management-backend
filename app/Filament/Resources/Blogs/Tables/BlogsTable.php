<?php

namespace App\Filament\Resources\Blogs\Tables;

use App\Enums\BlogStatus;
use App\Filament\Actions\TableExportAction;
use App\Filament\Resources\Blogs\BlogResource;
use App\Models\Blog;
use App\Services\BlogService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('admin.sr_no'))
                    ->sortable(),
                ImageColumn::make('cover_image')
                    ->label(__('admin.image'))
                    ->disk('public')
                    ->square()
                    ->size(60),
                TextColumn::make('title')
                    ->label(__('admin.blog_title'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label(__('admin.blog_category'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('admin.created_date'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.status'))
                    ->badge()
                    ->formatStateUsing(fn (BlogStatus $state): string => $state->label())
                    ->color(fn (BlogStatus $state): string => match ($state) {
                        BlogStatus::Published => 'success',
                        BlogStatus::Draft => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.status'))
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('blog_category_id')
                    ->label(__('admin.category'))
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                Action::make(__('admin.view'))
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (Blog $record): string => $record->title)
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalWidth('5xl')
                    ->modalContent(fn (Blog $record) => view('filament.pages.blogs.view-blog', ['blog' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('admin.close')),
                EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->url(fn (Blog $record): string => BlogResource::getUrl('edit', ['record' => $record])),
                Action::make(__('admin.delete'))
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-trash')
                    ->modalHeading(__('admin.delete_blog'))
                    ->modalDescription(__('admin.are_you_sure_you_want_to_delete_this_blog_this_action_can_be_undone'))
                    ->modalSubmitActionLabel(__('admin.yes_delete'))
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                    ->action(function (Blog $record): void {
                        app(BlogService::class)->deleteBlog($record);

                        Notification::make()
                            ->title(__('admin.blog_deleted_successfully'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                TableExportAction::make()
                    ->filename('blogs')
                    ->exports([
                        'title' => 'Blog Title',
                        'category.name' => 'Blog Category',
                        'status' => ['label' => 'Status', 'formatter' => fn (Blog $record): string => $record->status->label()],
                        'created_at' => ['label' => 'Created Date', 'formatter' => fn (Blog $record): string => $record->created_at->format('d M Y')],
                    ])
                    ->toActionGroup(),
                Action::make(__('admin.createnewblog'))
                    ->label(__('admin.create_new_blog'))
                    ->url(fn () => BlogResource::getUrl('create')),
            ])
            ->emptyStateHeading(__('admin.no_blogs_published_yet'))
            ->emptyStateDescription(__('admin.start_creating_content_to_educate_users_improve_seo_and_promote_your_platformnyour_published_blogs_will_appear_here_once_added'))
            ->emptyStateIcon('heroicon-o-queue-list')
            ->defaultPaginationPageOption(10)
            ->queryStringIdentifier('blogs');
    }
}
