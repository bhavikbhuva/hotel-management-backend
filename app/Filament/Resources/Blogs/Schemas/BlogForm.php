<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Models\Blog;
use App\Models\BlogCategory;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Blog Configuration')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Blog Title')
                            ->placeholder('e.g, Search your stay')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set, ?string $operation, $record): void {
                                $slug = Str::slug($state);
                                $original = $slug;
                                $counter = 2;
                                $ignoreId = $operation === 'edit' && $record ? $record->id : null;

                                while (Blog::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
                                    $slug = $original.'-'.$counter;
                                    $counter++;
                                }

                                $set('slug', $slug);
                            }),
                        TextInput::make('slug')
                            ->label('Slug (URL Friendly)')
                            ->placeholder('/blogs/')
                            ->required()
                            ->maxLength(255)
                            ->unique(table: Blog::class, column: 'slug', ignoreRecord: true)
                            ->validationMessages(['unique' => 'This slug is already taken.'])
                            ->helperText('The slug will be auto-generated from the blog title and can be edited if needed.'),
                        Select::make('blog_category_id')
                            ->label('Category')
                            ->placeholder('Select a Category')
                            ->options(BlogCategory::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live(onBlur: true)
                            ->suffixAction(
                                Action::make('manageCategories')
                                    ->label('Manage Categories')
                                    ->link()
                                    ->url(fn () => route('filament.admin.resources.blogs.index', ['tab' => 'categories']))
                                    ->openUrlInNewTab(),
                            ),
                        Textarea::make('short_description')
                            ->label('Short Description')
                            ->placeholder('Brief Description of the blog')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->live(onBlur: true),
                        RichEditor::make('content')
                            ->label('Main Content')
                            ->placeholder('Add Blog Details Page')
                            ->required()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link', 'textColor', 'highlight'],
                                ['h1', 'h2', 'h3', 'lead', 'small'],
                                ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList', 'horizontalRule'],
                                ['table', 'grid', 'details', 'attachFiles'],
                                ['undo', 'redo', 'clearFormatting'],
                            ])
                            ->floatingToolbars([
                                'table' => [
                                    'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                                    'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                                    'tableMergeCells', 'tableSplitCell',
                                    'tableToggleHeaderRow', 'tableDelete',
                                ],
                            ])
                            ->fileAttachmentsDirectory('blogs/content')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsMaxSize(2048),
                        FileUpload::make('cover_image')
                            ->label('Blog Cover Image')
                            ->image()
                            ->disk('public')
                            ->directory('blogs/covers')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->helperText('Maximum Size: 2MB | Resolution 1620×823 PX | Supported Files: JPG/PNG')
                            ->required()
                            ->live(),
                    ]),
                Section::make('Live Website Preview')
                    ->columnSpan(1)
                    ->schema([
                        View::make('filament.schemas.components.blog-preview'),
                    ]),
                Section::make('SEO Configuration')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->placeholder('SEO Title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->placeholder('SEO Description')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                        TextInput::make('keywords')
                            ->label('Keywords (Comma separated)')
                            ->placeholder('e.g, hotel, travel, luxury')
                            ->required()
                            ->maxLength(500),
                    ]),
            ]);
    }
}
