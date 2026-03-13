<?php

namespace App\Filament\Pages;

use App\Enums\TaxStatus;
use App\Enums\TaxType;
use App\Models\Tax;
use App\Services\TaxService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class TaxManage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'taxes';

    protected static ?string $title = 'Tax Management';

    protected static ?string $navigationLabel = 'Tax Manage';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.tax-manage';

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
        return 'Tax Management';
    }

    public function getSubheading(): ?string
    {
        return 'Configure taxes based on applicable regulations.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addNewTax')
                ->label('+ Add New Tax')
                ->modalHeading('Add New Tax')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Save Tax')
                ->modalFooterActionsAlignment(Alignment::End)
                ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                ->schema($this->getTaxFormSchema())
                ->action(function (array $data): void {
                    /** @var \App\Models\User $user */
                    $user = auth()->user();

                    app(TaxService::class)->createTax($data, $user->current_country_id);

                    Notification::make()
                        ->title('Tax created successfully.')
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
                Tax::query()->forCountry($user->current_country_id)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Tax Name')
                    ->description(fn (Tax $record): string => 'ID - '.str_pad((string) $record->id, 2, '0', STR_PAD_LEFT))
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (TaxType $state): string => $state->label())
                    ->color(fn (TaxType $state): string => match ($state) {
                        TaxType::Percentage => 'info',
                        TaxType::Fixed => 'warning',
                    }),
                TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function (Tax $record): string {
                        if ($record->type === TaxType::Percentage) {
                            return rtrim(rtrim(number_format((float) $record->value, 4), '0'), '.').'%';
                        }

                        $symbol = $record->country?->currency_symbol ?? '$';

                        return $symbol.rtrim(rtrim(number_format((float) $record->value, 4), '0'), '.');
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (TaxStatus $state): string => $state->label())
                    ->color(fn (TaxStatus $state): string => match ($state) {
                        TaxStatus::Active => 'success',
                        TaxStatus::Inactive => 'gray',
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
                ActionGroup::make([
                    Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->modalHeading('Edit Tax')
                        ->modalWidth('md')
                        ->modalSubmitActionLabel('Save Tax')
                        ->modalFooterActionsAlignment(Alignment::End)
                        ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                        ->fillForm(fn (Tax $record): array => [
                            'name' => $record->name,
                            'description' => $record->description,
                            'type' => $record->type->value,
                            'value' => $record->value,
                            'status' => $record->status->value,
                        ])
                        ->schema($this->getTaxFormSchema())
                        ->action(function (Tax $record, array $data): void {
                            app(TaxService::class)->updateTax($record, $data);

                            Notification::make()
                                ->title('Tax updated successfully.')
                                ->success()
                                ->send();
                        }),
                    Action::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->disabled(function (): bool {
                            /** @var \App\Models\User $user */
                            $user = auth()->user();

                            return Tax::query()->forCountry($user->current_country_id)->count() <= 1;
                        })
                        ->tooltip(function (): ?string {
                            /** @var \App\Models\User $user */
                            $user = auth()->user();

                            if (Tax::query()->forCountry($user->current_country_id)->count() <= 1) {
                                return 'At least one tax is required.';
                            }

                            return null;
                        })
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading('Delete Tax?')
                        ->modalDescription('Are you sure you want to delete? This tax will no longer be applied to future bookings.')
                        ->modalSubmitActionLabel('Yes, Delete')
                        ->modalFooterActionsAlignment(Alignment::End)
                        ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                        ->action(function (Tax $record): void {
                            app(TaxService::class)->deleteTax($record);

                            Notification::make()
                                ->title('Tax deleted successfully.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultPaginationPageOption(4);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getTaxFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Tax Name')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Description')
                ->required()
                ->maxLength(1000),
            Select::make('type')
                ->label('Calculation Type')
                ->options([
                    'percentage' => 'Percentage',
                    'fixed' => 'Fixed',
                ])
                ->required()
                ->live(),
            TextInput::make('value')
                ->label('Rate Value')
                ->required()
                ->numeric()
                ->suffix(function (Get $get): ?string {
                    $type = $get('type');

                    if ($type === 'percentage') {
                        return '%';
                    }

                    if ($type === 'fixed') {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();

                        return $user->currentCountry?->currency_symbol ?? '$';
                    }

                    return null;
                }),
            Radio::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active')
                ->required(),
        ];
    }
}
