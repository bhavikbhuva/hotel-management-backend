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
                    ->label('SR. NO')
                    ->sortable(),
                ImageColumn::make('cover_image')
                    ->label('IMAGE')
                    ->disk('public')
                    ->square()
                    ->size(60),
                TextColumn::make('title')
                    ->label('BLOG TITLE')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label('BLOG CATEGORY')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('CREATED DATE')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->formatStateUsing(fn (BlogStatus $state): string => $state->label())
                    ->color(fn (BlogStatus $state): string => match ($state) {
                        BlogStatus::Published => 'success',
                        BlogStatus::Draft => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('blog_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                Action::make('view')
                    ->iconButton()
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (Blog $record): string => $record->title)
                    ->modalWidth('5xl')
                    ->modalContent(fn (Blog $record) => view('filament.pages.blogs.view-blog', ['blog' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->url(fn (Blog $record): string => BlogResource::getUrl('edit', ['record' => $record])),
                Action::make('delete')
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-trash')
                    ->modalHeading('Delete Blog?')
                    ->modalDescription('Are you sure you want to delete this blog? This action can be undone.')
                    ->modalSubmitActionLabel('Yes, Delete')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                    ->action(function (Blog $record): void {
                        app(BlogService::class)->deleteBlog($record);

                        Notification::make()
                            ->title('Blog deleted successfully.')
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
                Action::make('createNewBlog')
                    ->label('+ Create New Blog')
                    ->url(fn () => BlogResource::getUrl('create')),
            ])
            ->emptyStateHeading('No Blogs Published Yet')
            ->emptyStateDescription("Start creating content to educate users, improve SEO, and promote your platform.\nYour published blogs will appear here once added.")
            ->emptyStateIcon('heroicon-o-queue-list')
            ->defaultPaginationPageOption(10)
            ->queryStringIdentifier('blogs');
    }
}
