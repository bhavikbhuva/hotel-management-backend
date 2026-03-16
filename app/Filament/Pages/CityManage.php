<?php

namespace App\Filament\Pages;

use App\Enums\CityStatus;
use App\Filament\Actions\TableExportAction;
use App\Models\City;
use App\Models\RefCity;
use App\Models\RefState;
use App\Services\CityService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CityManage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'cities';

    protected static ?string $title = 'City Management';

    protected static ?string $navigationLabel = 'City Manage';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.city-manage';

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
                ->schema($this->getCityFormSchema())
                ->action(function (array $data): void {
                    /** @var \App\Models\User $user */
                    $user = auth()->user();

                    app(CityService::class)->createCity($data, $user->current_country_id);

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
                TextColumn::make('state.name')
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
                        $refStateId = $record->state?->ref_state_id;

                        // State-as-city fallback: ref_city_id is null
                        $refCityValue = $record->ref_city_id
                            ? $record->ref_city_id
                            : ($refStateId ? "state:{$refStateId}" : null);

                        $form->fill([
                            'ref_state_id' => $refStateId,
                            'ref_city_id' => $refCityValue,
                            'status' => $record->status->value,
                        ]);
                    })
                    ->schema(fn (City $record): array => $this->getCityFormSchema(isEdit: true, excludeRefCityId: $record->ref_city_id, excludeCityId: $record->id))
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

                        app(CityService::class)->updateCity($record, $data, $user->current_country_id);

                        Notification::make()
                            ->title('City updated successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                TableExportAction::make()
                    ->filename('cities')
                    ->exports([
                        'name' => 'City Name',
                        'state.name' => 'State/Province',
                        'latitude' => 'Latitude',
                        'longitude' => 'Longitude',
                        'status' => ['label' => 'Status', 'formatter' => fn (City $record): string => $record->status->label()],
                    ])
                    ->toActionGroup(),
            ])
            ->emptyStateHeading('No Cities Added Yet')
            ->emptyStateDescription('Cities are used to help customers search and discover properties on the website. Add cities to improve location-based search results and user experience.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->defaultPaginationPageOption(4);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component|\Filament\Schemas\Components\Component>
     */
    private function getCityFormSchema(bool $isEdit = false, ?int $excludeRefCityId = null, ?int $excludeCityId = null): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $country = $user->currentCountry;
        $countryName = e($country->name);
        $isoCode = e($country->iso_code);
        $flagUrl = asset('assets/flags/'.strtolower($country->iso_code).'.svg');
        $refCountryId = $country->ref_country_id;

        // Get already-added ref_city_ids to exclude from the dropdown
        $addedRefCityIds = City::query()
            ->forCountry($user->current_country_id)
            ->whereNotNull('ref_city_id')
            ->when($excludeRefCityId, fn ($q) => $q->where('ref_city_id', '!=', $excludeRefCityId))
            ->pluck('ref_city_id')
            ->all();

        // Get ref_state_ids that already have a state-as-city entry (ref_city_id is null)
        $addedStateAsCityRefStateIds = City::query()
            ->where('cities.country_id', $user->current_country_id)
            ->whereNull('cities.ref_city_id')
            ->when($excludeCityId, fn ($q) => $q->where('cities.id', '!=', $excludeCityId))
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->pluck('states.ref_state_id')
            ->all();

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

        if ($isEdit) {
            $schema[] = Text::make(new HtmlString(
                '<div class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20">'.
                '<p class="text-sm text-gray-600 dark:text-gray-300">'.
                '<span class="font-medium">Warning:</span> Changing the city details may affect properties linked to this city.'.
                '</p>'.
                '</div>'
            ));
        }

        $schema[] = Select::make('ref_state_id')
            ->label('State / Province')
            ->options(fn (): Collection => RefState::query()
                ->where('country_id', $refCountryId)
                ->orderBy('name')
                ->pluck('name', 'id'))
            ->searchable()
            ->required()
            ->live()
            ->afterStateUpdated(fn (Select $component) => $component
                ->getContainer()
                ->getComponent('citySelectGrid')
                ->getChildSchema()
                ->fill());

        $schema[] = Grid::make(1)
            ->schema(fn (Get $get): array => [
                Select::make('ref_city_id')
                    ->label('City')
                    ->options(function () use ($get, $addedRefCityIds, $addedStateAsCityRefStateIds): Collection {
                        $stateId = $get('ref_state_id');
                        if (! $stateId) {
                            return collect();
                        }

                        $cities = RefCity::query()
                            ->where('state_id', $stateId)
                            ->when($addedRefCityIds, fn ($q) => $q->whereNotIn('id', $addedRefCityIds))
                            ->orderBy('name')
                            ->pluck('name', 'id');

                        // Fallback: only offer the state itself when the state has NO
                        // ref_cities at all (not when they're all filtered out as already added)
                        if ($cities->isEmpty() && ! RefCity::query()->where('state_id', $stateId)->exists()) {
                            if (in_array((int) $stateId, $addedStateAsCityRefStateIds, true)) {
                                return collect();
                            }

                            $refState = RefState::find($stateId);
                            if ($refState) {
                                return collect(["state:{$stateId}" => "{$refState->name} (state)"]);
                            }
                        }

                        return $cities;
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if (! $state) {
                            $set('latitude', null);
                            $set('longitude', null);

                            return;
                        }

                        // Handle state-as-city fallback
                        if (str_starts_with((string) $state, 'state:')) {
                            $refState = RefState::find((int) str_replace('state:', '', $state));
                            if ($refState) {
                                $set('latitude', $refState->latitude);
                                $set('longitude', $refState->longitude);
                            }

                            return;
                        }

                        $refCity = RefCity::find($state);
                        if ($refCity) {
                            $set('latitude', $refCity->latitude);
                            $set('longitude', $refCity->longitude);
                        }
                    }),
            ])
            ->key('citySelectGrid');

        $schema[] = Grid::make(2)
            ->schema([
                \Filament\Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                \Filament\Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
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
