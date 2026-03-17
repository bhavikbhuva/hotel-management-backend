<?php

namespace App\Filament\Pages;

use App\Models\HowItWorksStep;
use App\Services\HowItWorksService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class HelpSupportManage extends Page
{
    protected static ?string $slug = 'help-support';

    protected static ?string $title = 'How It Works Steps';

    protected static ?string $navigationLabel = 'Help & Support FAQs';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.help-support-manage';

    #[Url(as: 'tab')]
    public string $currentTab = 'how-it-works';

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Content Management';
    }

    public function getHeading(): string|Htmlable
    {
        return match ($this->currentTab) {
            'faqs' => 'Topics & FAQs',
            default => 'How It Works Steps',
        };
    }

    public function getSubheading(): ?string
    {
        return match ($this->currentTab) {
            'faqs' => 'Create topics and add frequently asked questions to them.',
            default => 'Manage the steps displayed in the "How It Works" section.',
        };
    }

    public function switchTab(string $tab): void
    {
        $this->currentTab = $tab;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function canAddStep(): bool
    {
        return app(HowItWorksService::class)->canAddStep();
    }

    public function addStepAction(): Action
    {
        return Action::make('addStep')
            ->label('+ Add Steps')
            ->modalHeading('Add New Step')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Add Step')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getStepFormSchema())
            ->action(function (array $data): void {
                if (! app(HowItWorksService::class)->canAddStep()) {
                    Notification::make()
                        ->title('Maximum of '.HowItWorksService::MAX_STEPS.' steps allowed.')
                        ->danger()
                        ->send();

                    return;
                }

                app(HowItWorksService::class)->createStep($data);

                Notification::make()
                    ->title('Step added successfully.')
                    ->success()
                    ->send();
            });
    }

    public function editStepAction(): Action
    {
        return Action::make('editStep')
            ->iconButton()
            ->icon('heroicon-o-pencil')
            ->color('gray')
            ->modalHeading('Edit Step')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Save Step')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getStepFormSchema())
            ->fillForm(fn (array $arguments): array => [
                'title' => HowItWorksStep::find($arguments['step'])?->title,
                'description' => HowItWorksStep::find($arguments['step'])?->description,
            ])
            ->action(function (array $data, array $arguments): void {
                $step = HowItWorksStep::find($arguments['step']);

                if (! $step) {
                    return;
                }

                app(HowItWorksService::class)->updateStep($step, $data);

                Notification::make()
                    ->title('Step updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteStepAction(): Action
    {
        return Action::make('deleteStep')
            ->iconButton()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading('Delete Step?')
            ->modalDescription('Are you sure you want to delete this step? It will be removed from the "How It Works" section.')
            ->modalSubmitActionLabel('Yes, Delete')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->action(function (array $arguments): void {
                $step = HowItWorksStep::find($arguments['step']);

                if (! $step) {
                    return;
                }

                app(HowItWorksService::class)->deleteStep($step);

                Notification::make()
                    ->title('Step deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public function getSteps(): Collection
    {
        return HowItWorksStep::query()->orderBy('sort_order')->get();
    }

    public function getHasSteps(): bool
    {
        return HowItWorksStep::query()->exists();
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getStepFormSchema(): array
    {
        return [
            Placeholder::make('note')
                ->label('')
                ->content(new HtmlString(
                    '<div class="rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">'.
                    '<p class="text-sm"><span class="font-semibold text-red-600 dark:text-red-400">Note:</span> '.
                    '<span class="text-red-600 dark:text-red-400">A maximum of '.HowItWorksService::MAX_STEPS.' steps can be displayed. You can add or remove steps to stay within this limit.</span></p>'.
                    '</div>'
                )),
            TextInput::make('title')
                ->label('Title')
                ->placeholder('e.g, Search your stay')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Description')
                ->placeholder('Brief Description of the steps...')
                ->required()
                ->maxLength(90)
                ->live(onBlur: false, debounce: 300)
                ->helperText(fn ($state): string => 'Character Limit: '.strlen($state ?? '').' / 90')
                ->rows(3),
        ];
    }
}
