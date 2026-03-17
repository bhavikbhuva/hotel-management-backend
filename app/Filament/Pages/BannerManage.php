<?php

namespace App\Filament\Pages;

use App\Enums\BannerStatus;
use App\Filament\Actions\TableExportAction;
use App\Models\Banner;
use App\Models\User;
use App\Services\BannerService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class BannerManage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'banners';

    protected static ?string $title = 'Banner Management';

    protected static ?string $navigationLabel = 'Banner & Advertisement';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.banner-manage';

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Marketing';
    }   

    public function getHeading(): string|Htmlable
    {
        return 'Banner Management';
    }

    public function getHasBanners(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return Banner::query()->forCountry($user->current_country_id)->exists();
    }

    public function getSubheading(): ?string
    {
        return 'Manage home screen banners for App and Web.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addNewBanner')
                ->label('+ Add New Banner')
                ->modalHeading('Add New Banner')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Add Banner')
                ->modalFooterActionsAlignment(Alignment::End)
                ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                ->schema($this->getBannerFormSchema())
                ->action(function (array $data): void {
                    /** @var User $user */
                    $user = auth()->user();

                    app(BannerService::class)->createBanner($data, $user->current_country_id);

                    Notification::make()
                        ->title('Banner created successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        /** @var User $user */
        $user = auth()->user();

        return $table
            ->query(
                Banner::query()->forCountry($user->current_country_id)
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('BANNER')
                    ->disk('public')
                    ->square()
                    ->size(60),
                TextColumn::make('title')
                    ->label('BANNER')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('BANNER DATES')
                    ->formatStateUsing(function (Banner $record): HtmlString {
                        if (! $record->start_date && ! $record->end_date) {
                            return new HtmlString('<span class="text-gray-500">Always Active</span>');
                        }

                        $isScheduled = $record->start_date && $record->start_date->isFuture();
                        $lines = [];

                        if ($isScheduled) {
                            $lines[] = '<span class="font-medium text-warning-600 dark:text-warning-400">Scheduled</span>';
                        }

                        if ($record->start_date) {
                            $lines[] = 'Start: '.$record->start_date->format('M d, Y');
                        }

                        if ($record->end_date) {
                            $lines[] = 'End: '.$record->end_date->format('M d, Y');
                        }

                        return new HtmlString(implode('<br>', $lines));
                    }),
                TextColumn::make('target_url')
                    ->label('TARGET LINK')
                    ->limit(40)
                    ->url(fn (Banner $record): string => $record->target_url)
                    ->openUrlInNewTab()
                    ->color('primary'),
                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->formatStateUsing(fn (BannerStatus $state): string => $state->label())
                    ->color(fn (BannerStatus $state): string => match ($state) {
                        BannerStatus::Active => 'success',
                        BannerStatus::Inactive => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                Action::make('edit')
                    ->iconButton()
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->modalHeading('Edit Banner')
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Save Banner')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                    ->fillForm(fn (Banner $record): array => [
                        'start_date' => $record->start_date?->format('Y-m-d'),
                        'end_date' => $record->end_date?->format('Y-m-d'),
                        'title' => $record->title,
                        'image' => $record->image,
                        'target_url' => $record->target_url,
                        'status' => $record->status->value,
                    ])
                    ->schema($this->getBannerFormSchema(isCreate: false))
                    ->action(function (Banner $record, array $data): void {
                        app(BannerService::class)->updateBanner($record, $data);

                        Notification::make()
                            ->title('Banner updated successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-trash')
                    ->modalHeading('Delete Banner?')
                    ->modalDescription('Are you sure you want to delete this banner? It will no longer be displayed to users.')
                    ->modalSubmitActionLabel('Yes, Delete')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                    ->action(function (Banner $record): void {
                        app(BannerService::class)->deleteBanner($record);

                        Notification::make()
                            ->title('Banner deleted successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                TableExportAction::make()
                    ->filename('banners')
                    ->exports([
                        'title' => 'Banner Title',
                        'target_url' => 'Target URL',
                        'start_date' => ['label' => 'Start Date', 'formatter' => fn (Banner $record): string => $record->start_date?->format('d M Y') ?? '-'],
                        'end_date' => ['label' => 'End Date', 'formatter' => fn (Banner $record): string => $record->end_date?->format('d M Y') ?? '-'],
                        'status' => ['label' => 'Status', 'formatter' => fn (Banner $record): string => $record->status->label()],
                    ])
                    ->toActionGroup(),
            ])
            ->emptyStateHeading("You haven't added any banners yet.")
            ->emptyStateDescription("Banners are displayed as sliders on the App and Web home screens. Add banners\nto control the images shown to users.")
            ->emptyStateIcon('heroicon-o-flag')
            ->defaultPaginationPageOption(10);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getBannerFormSchema(bool $isCreate = true): array
    {
        /** @var User $user */
        $user = auth()->user();
        $country = $user->currentCountry;

        return [
            Placeholder::make('selected_country')
                ->label('Selected Country')
                ->content(new HtmlString(
                    '<div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">'.
                    '<img src="/assets/flags/'.strtolower($country?->iso_code ?? 'us').'.svg" class="h-8 w-8 rounded-full object-cover" alt="Flag" />'.
                    '<div>'.
                    '<div class="font-medium text-gray-900 dark:text-white">'.e($country?->name ?? 'Unknown').'</div>'.
                    '<div class="text-xs text-gray-500 dark:text-gray-400">ISO · '.e($country?->iso_code ?? '-').'</div>'.
                    '</div>'.
                    '</div>'.
                    '<p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Note: Banners are country-specific. This banner will apply only to the selected country.</p>'
                )),
            DatePicker::make('start_date')
                ->label('Start Date')
                ->minDate($isCreate ? now()->toDateString() : null)
                ->columnSpan(1),
            DatePicker::make('end_date')
                ->label('End Date')
                ->afterOrEqual('start_date')
                ->minDate($isCreate ? now()->toDateString() : null)
                ->columnSpan(1),
            TextInput::make('title')
                ->label('Banner Title')
                ->placeholder('e.g, Best Hotel')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            FileUpload::make('image')
                ->label('Upload Banner')
                ->image()
                ->disk('public')
                ->directory('banners')
                ->visibility('public')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                ->helperText('Maximum Size: 2MB | Resolution 1872×750 PX | Supported Files: JPG/PNG')
                ->required()
                ->columnSpanFull(),
            TextInput::make('target_url')
                ->label('External Target Link')
                ->placeholder('e.g, https://...')
                ->required()
                ->url()
                ->maxLength(2048)
                ->columnSpanFull(),
            Radio::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active')
                ->required()
                ->inline()
                ->columnSpanFull(),
        ];
    }
}
