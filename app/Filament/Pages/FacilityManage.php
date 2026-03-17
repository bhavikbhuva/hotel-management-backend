<?php

namespace App\Filament\Pages;

use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Services\FacilityCategoryService;
use App\Services\FacilityService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

class FacilityManage extends Page
{
    protected static ?string $slug = 'facilities';

    protected static ?string $title = 'Facilities & Amenities';

    protected static ?string $navigationLabel = 'Facilities & Amenities';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.facility-manage';

    public string $search = '';

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Property Management';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Facilities & Amenities';
    }

    public function getSubheading(): ?string
    {
        return 'Manage property facilities and amenities.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    // ──────────────────────────────────────────────
    // Header Actions
    // ──────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addCategory')
                ->label('+ Add Facility Category')
                ->modalHeading('Add New Facility Category')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Create Category')
                ->modalFooterActionsAlignment(Alignment::End)
                ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
                ->schema($this->getCategoryFormSchema())
                ->action(function (array $data): void {
                    app(FacilityCategoryService::class)->createCategory($data);

                    Notification::make()
                        ->title('Category created successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    // ──────────────────────────────────────────────
    // Category Actions
    // ──────────────────────────────────────────────

    public function editCategoryAction(): Action
    {
        return Action::make('editCategory')
            ->iconButton()
            ->icon('heroicon-o-pencil')
            ->color('gray')
            ->modalHeading('Edit Facility Category')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Save Changes')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getCategoryFormSchema())
            ->fillForm(function (array $arguments): array {
                $category = FacilityCategory::find($arguments['category']);

                return [
                    'name' => $category?->name,
                    'icon' => $category?->icon,
                    'status' => $category?->status->value,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $category = FacilityCategory::find($arguments['category']);

                if (! $category) {
                    return;
                }

                app(FacilityCategoryService::class)->updateCategory($category, $data);

                Notification::make()
                    ->title('Category updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteCategoryAction(): Action
    {
        return Action::make('deleteCategory')
            ->iconButton()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading('Delete Category?')
            ->modalDescription('Are you sure you want to delete this category? This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, Delete')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->action(function (array $arguments): void {
                $category = FacilityCategory::find($arguments['category']);

                if (! $category) {
                    return;
                }

                try {
                    app(FacilityCategoryService::class)->deleteCategory($category);

                    Notification::make()
                        ->title('Category deleted successfully.')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Cannot delete category')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    // ──────────────────────────────────────────────
    // Facility Actions
    // ──────────────────────────────────────────────

    public function addFacilityAction(): Action
    {
        return Action::make('addFacility')
            ->label('+ Add Amenity')
            ->color('dark')
            ->modalHeading(function (array $arguments): string {
                $category = FacilityCategory::find($arguments['category']);

                return 'Add New Facility in '.($category?->name ?? 'Unknown');
            })
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Create Facility')
            ->modalSubmitAction(fn (Action $action) => $action->color('primary'))
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getFacilityFormSchema())
            ->action(function (array $data, array $arguments): void {
                $categoryId = $arguments['category'] ?? null;

                if (! $categoryId) {
                    return;
                }

                app(FacilityService::class)->createFacility($categoryId, $data);

                Notification::make()
                    ->title('Facility created successfully.')
                    ->success()
                    ->send();
            });
    }

    public function editFacilityAction(): Action
    {
        return Action::make('editFacility')
            ->iconButton()
            ->icon('heroicon-o-pencil')
            ->color('gray')
            ->modalHeading(function (array $arguments): string {
                $facility = Facility::with('category')->find($arguments['facility']);

                return 'Edit Facility in '.($facility?->category?->name ?? 'Unknown');
            })
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Save Changes')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getFacilityFormSchema())
            ->fillForm(function (array $arguments): array {
                $facility = Facility::find($arguments['facility']);

                return [
                    'name' => $facility?->name,
                    'icon' => $facility?->icon,
                    'status' => $facility?->status->value,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $facility = Facility::find($arguments['facility']);

                if (! $facility) {
                    return;
                }

                app(FacilityService::class)->updateFacility($facility, $data);

                Notification::make()
                    ->title('Facility updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteFacilityAction(): Action
    {
        return Action::make('deleteFacility')
            ->iconButton()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading('Delete Amenities & Facilities')
            ->modalDescription('Are you sure you want to delete this facility or amenity? Deleting this facility or amenity will remove it from all associated properties permanently.')
            ->modalSubmitActionLabel('Yes, Delete')
            ->modalSubmitAction(fn (Action $action) => $action->color('danger'))
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->action(function (array $arguments): void {
                $facility = Facility::find($arguments['facility']);

                if (! $facility) {
                    return;
                }

                app(FacilityService::class)->deleteFacility($facility);

                Notification::make()
                    ->title('Facility deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    // ──────────────────────────────────────────────
    // Data Methods
    // ──────────────────────────────────────────────

    public function getCategories(): Collection
    {
        $search = trim($this->search);

        return FacilityCategory::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('facilities', fn ($fq) => $fq->where('name', 'like', "%{$search}%"));
                });
            })
            ->withCount(['facilities' => function ($query) use ($search): void {
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
            }])
            ->with(['facilities' => function ($query) use ($search): void {
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
    }

    public function getHasCategories(): bool
    {
        return FacilityCategory::query()->exists();
    }

    // ──────────────────────────────────────────────
    // Form Schemas
    // ──────────────────────────────────────────────

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getCategoryFormSchema(): array
    {
        return [
            FileUpload::make('icon')
                ->label('Facility Category Icon')
                ->disk('public')
                ->directory('facility-categories')
                ->visibility('public')
                ->maxSize(5120)
                ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                ->helperText('Maximum Size: 5MB | Supported Files: PNG/SVG')
                ->required()
                ->columnSpanFull(),
            TextInput::make('name')
                ->label('Category Name')
                ->placeholder('Enter Category Name')
                ->required()
                ->maxLength(255)
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

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getFacilityFormSchema(): array
    {
        return [
            FileUpload::make('icon')
                ->label('Facility Icon')
                ->disk('public')
                ->directory('facilities')
                ->visibility('public')
                ->maxSize(5120)
                ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                ->helperText('Maximum Size: 5MB | Supported Files: PNG/SVG')
                ->required()
                ->columnSpanFull(),
            TextInput::make('name')
                ->label('Facility Name')
                ->placeholder('Enter Facility Name')
                ->required()
                ->maxLength(255)
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
