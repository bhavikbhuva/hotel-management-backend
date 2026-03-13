<?php

namespace App\Filament\Pages;

use App\Enums\CityStatus;
use App\Models\City;
use App\Models\Setting;
use App\Services\CityService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class CityManage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'cities';

    protected static ?string $title = 'City Management';

    protected static ?string $navigationLabel = 'City Manage';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.city-manage';

    public ?string $selectedState = null;

    public ?string $selectedGooglePlaceId = null;

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Location & Policies';
    }

    public function getHeading(): string|Htmlable
    {
        return 'City Management';
    }

    public function getSubheading(): ?string
    {
        return 'Manage city boundaries, states, and location services.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addNewCity')
                ->label('+ Add New City')
                ->modalHeading('Add New City')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Add City')
                ->modalFooterActionsAlignment(Alignment::End)
                ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                ->mountUsing(function (Schema $form): void {
                    $this->selectedState = null;
                    $this->selectedGooglePlaceId = null;

                    $form->fill();
                })
                ->schema($this->getCityFormSchema())
                ->action(function (array $data): void {
                    /** @var \App\Models\User $user */
                    $user = auth()->user();

                    $data['state'] = $this->selectedState;
                    $data['google_place_id'] = $this->selectedGooglePlaceId;

                    app(CityService::class)->createCity($data, $user->current_country_id);

                    $this->selectedState = null;
                    $this->selectedGooglePlaceId = null;

                    Notification::make()
                        ->title('City created successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $table
            ->query(
                City::query()->forCountry($user->current_country_id)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('City Name')
                    ->description(fn (City $record): string => strtoupper($record->country?->iso_code ?? ''))
                    ->searchable(),
                TextColumn::make('state')
                    ->label('State/Province')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->formatStateUsing(function (string $state): string {
                        $value = (float) $state;
                        $direction = $value >= 0 ? 'N' : 'S';

                        return number_format(abs($value), 2).' '.$direction;
                    })
                    ->color('info'),
                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->formatStateUsing(function (string $state): string {
                        $value = (float) $state;
                        $direction = $value >= 0 ? 'E' : 'W';

                        return number_format(abs($value), 2).' '.$direction;
                    })
                    ->color('info'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (CityStatus $state): string => $state->label())
                    ->color(fn (CityStatus $state): string => match ($state) {
                        CityStatus::Active => 'success',
                        CityStatus::Inactive => 'gray',
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
                    ->icon('heroicon-o-pencil')
                    ->iconButton()
                    ->tooltip('Edit')
                    ->modalHeading('Edit City')
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Save City')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                    ->mountUsing(function (Schema $form, City $record): void {
                        $this->selectedState = $record->state;
                        $this->selectedGooglePlaceId = $record->google_place_id;

                        $form->fill([
                            'name' => $record->name,
                            'latitude' => $record->latitude,
                            'longitude' => $record->longitude,
                            'status' => $record->status->value,
                        ]);
                    })
                    ->schema($this->getCityFormSchema(isEdit: true))
                    ->action(function (City $record, array $data): void {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();

                        if ($data['status'] === 'inactive' && $record->status === CityStatus::Active) {
                            $activeCityCount = City::query()
                                ->forCountry($user->current_country_id)
                                ->active()
                                ->count();

                            if ($activeCityCount <= 1) {
                                Notification::make()
                                    ->title('At least one active city is required.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                        }

                        $data['state'] = $this->selectedState;
                        $data['google_place_id'] = $this->selectedGooglePlaceId;

                        app(CityService::class)->updateCity($record, $data);

                        $this->selectedState = null;
                        $this->selectedGooglePlaceId = null;

                        Notification::make()
                            ->title('City updated successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No Cities Added Yet')
            ->emptyStateDescription('Cities are used to help customers search and discover properties on the website. Add cities to improve location-based search results and user experience.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->defaultPaginationPageOption(4);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getCityFormSchema(bool $isEdit = false): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $country = $user->currentCountry;
        $countryName = e($country->name);
        $isoCode = e($country->iso_code);
        $flagUrl = asset('assets/flags/'.strtolower($country->iso_code).'.svg');
        $hasApiKey = (bool) Setting::get('google_places_api_key');

        $schema = [];

        $schema[] = Text::make(new HtmlString(
            '<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">'.
            '<p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Selected Country</p>'.
            '<div class="flex items-center gap-3">'.
            '<img src="'.$flagUrl.'" class="h-6 w-8 rounded object-cover" alt="'.$countryName.'">'.
            '<div>'.
            '<p class="font-semibold text-gray-900 dark:text-white">'.$countryName.'</p>'.
            '<p class="text-xs text-gray-500 dark:text-gray-400">ISO · '.$isoCode.'</p>'.
            '</div>'.
            '</div>'.
            '</div>'
        ));

        $schema[] = Text::make(new HtmlString(
            '<div class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/20">'.
            '<p class="text-sm text-gray-600 dark:text-gray-300">'.
            '<span class="font-medium">Note:</span> City will be added under the selected country. Only cities belonging to '.$countryName.' can be added here.'.
            '</p>'.
            '</div>'
        ));

        if (! $hasApiKey) {
            $schema[] = Text::make(new HtmlString(
                '<div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">'.
                '<div class="flex items-start gap-3">'.
                '<div class="flex-shrink-0">'.
                '<svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">'.
                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />'.
                '</svg>'.
                '</div>'.
                '<div>'.
                '<p class="font-semibold text-gray-900 dark:text-white">Places API not configured.</p>'.
                '<p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Please add your Google Places API key in system settings before creating a city.</p>'.
                '<button type="button" class="mt-2 inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white opacity-75 cursor-not-allowed" disabled>'.
                'Configure API'.
                '<svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">'.
                '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />'.
                '</svg>'.
                '</button>'.
                '</div>'.
                '</div>'.
                '</div>'
            ));
        }

        if ($isEdit) {
            $schema[] = Text::make(new HtmlString(
                '<div class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20">'.
                '<p class="text-sm text-gray-600 dark:text-gray-300">'.
                '<span class="font-medium">Warning:</span> Changing the city details may affect properties linked to this city.'.
                '</p>'.
                '</div>'
            ));
        }

        $schema[] = TextInput::make('name')
            ->label('City Name')
            ->placeholder('e.g, New Delhi')
            ->required()
            ->maxLength(255)
            ->disabled(! $hasApiKey)
            ->dehydrated()
            ->extraInputAttributes(['data-field' => 'city-name']);

        $schema[] = Grid::make(2)
            ->schema([
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->extraInputAttributes(['data-field' => 'latitude']),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->extraInputAttributes(['data-field' => 'longitude']),
            ]);

        $schema[] = Radio::make('status')
            ->label('Status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ])
            ->default('active')
            ->required()
            ->inline();

        return $schema;
    }
}
