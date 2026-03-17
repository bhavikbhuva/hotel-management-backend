<?php

namespace App\Filament\Pages;

use App\Models\Faq;
use App\Models\FaqTopic;
use App\Models\HowItWorksStep;
use App\Services\FaqService;
use App\Services\FaqTopicService;
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
use Illuminate\Support\Str;
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
            ->fillForm(function (array $arguments): array {
                $step = HowItWorksStep::find($arguments['step']);

                return [
                    'title' => $step?->title,
                    'description' => $step?->description,
                ];
            })
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

    // ──────────────────────────────────────────────
    // Topic Actions
    // ──────────────────────────────────────────────

    public function addTopicAction(): Action
    {
        return Action::make('addTopic')
            ->label('+ Add Topic')
            ->modalHeading('Add New Topic')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Add Topic')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getTopicFormSchema())
            ->action(function (array $data): void {
                app(FaqTopicService::class)->createTopic($data);

                Notification::make()
                    ->title('Topic added successfully.')
                    ->success()
                    ->send();
            });
    }

    public function editTopicAction(): Action
    {
        return Action::make('editTopic')
            ->iconButton()
            ->icon('heroicon-o-pencil')
            ->color('gray')
            ->modalHeading('Edit Topic')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Save Topic')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema($this->getTopicFormSchema(isCreate: false))
            ->fillForm(function (array $arguments): array {
                $topic = FaqTopic::find($arguments['topic']);

                return [
                    'title' => $topic?->title,
                    'slug' => $topic?->slug,
                    'description' => $topic?->description,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $topic = FaqTopic::find($arguments['topic']);

                if (! $topic) {
                    return;
                }

                app(FaqTopicService::class)->updateTopic($topic, $data);

                Notification::make()
                    ->title('Topic updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteTopicAction(): Action
    {
        return Action::make('deleteTopic')
            ->iconButton()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading('Delete Topic?')
            ->modalDescription('Are you sure you want to delete this topic? All FAQs under this topic will also be deleted.')
            ->modalSubmitActionLabel('Yes, Delete')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->action(function (array $arguments): void {
                $topic = FaqTopic::find($arguments['topic']);

                if (! $topic) {
                    return;
                }

                app(FaqTopicService::class)->deleteTopic($topic);

                Notification::make()
                    ->title('Topic deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    // ──────────────────────────────────────────────
    // FAQ Actions
    // ──────────────────────────────────────────────

    public function addFaqAction(): Action
    {
        return Action::make('addFaq')
            ->link()
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->label('Add FAQ')
            ->modalHeading('Add New FAQ')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Add FAQ')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema(fn (array $arguments): array => $this->getFaqFormSchema($arguments['topic'] ?? null))
            ->action(function (array $data, array $arguments): void {
                $topicId = $arguments['topic'] ?? null;

                if (! $topicId) {
                    return;
                }

                app(FaqService::class)->createFaq($topicId, $data);

                Notification::make()
                    ->title('FAQ added successfully.')
                    ->success()
                    ->send();
            });
    }

    public function editFaqAction(): Action
    {
        return Action::make('editFaq')
            ->iconButton()
            ->icon('heroicon-o-pencil')
            ->color('gray')
            ->modalHeading('Edit FAQ')
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Save FAQ')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->schema(function (array $arguments): array {
                $faq = Faq::with('topic')->find($arguments['faq']);

                return $this->getFaqFormSchema($faq?->faq_topic_id);
            })
            ->fillForm(function (array $arguments): array {
                $faq = Faq::find($arguments['faq']);

                return [
                    'question' => $faq?->question,
                    'answer' => $faq?->answer,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $faq = Faq::find($arguments['faq']);

                if (! $faq) {
                    return;
                }

                app(FaqService::class)->updateFaq($faq, $data);

                Notification::make()
                    ->title('FAQ updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteFaqAction(): Action
    {
        return Action::make('deleteFaq')
            ->iconButton()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading('Delete FAQ?')
            ->modalDescription('Are you sure you want to delete this FAQ?')
            ->modalSubmitActionLabel('Yes, Delete')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->action(function (array $arguments): void {
                $faq = Faq::find($arguments['faq']);

                if (! $faq) {
                    return;
                }

                app(FaqService::class)->deleteFaq($faq);

                Notification::make()
                    ->title('FAQ deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    // ──────────────────────────────────────────────
    // Data Methods
    // ──────────────────────────────────────────────

    public function getTopics(): Collection
    {
        return FaqTopic::query()
            ->withCount('faqs')
            ->with(['faqs' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    public function getHasTopics(): bool
    {
        return FaqTopic::query()->exists();
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

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getTopicFormSchema(bool $isCreate = true): array
    {
        return [
            TextInput::make('title')
                ->label('Topic Title')
                ->placeholder('e.g, Booking & Reservations')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $state, callable $set) use ($isCreate): void {
                    if ($isCreate) {
                        $set('slug', Str::slug($state));
                    }
                }),
            TextInput::make('slug')
                ->label('Slug')
                ->placeholder('auto-generated-from-title')
                ->required($isCreate)
                ->maxLength(255)
                ->disabled(! $isCreate)
                ->helperText($isCreate
                    ? 'Auto-generated from title. You can edit it manually.'
                    : 'Slug cannot be changed after creation.'),
            Textarea::make('description')
                ->label('Description')
                ->placeholder('Brief description of this topic...')
                ->required()
                ->maxLength(500)
                ->live(onBlur: false, debounce: 300)
                ->helperText(fn ($state): string => 'Character Limit: '.strlen($state ?? '').' / 500')
                ->rows(3),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getFaqFormSchema(?int $topicId): array
    {
        $topicName = $topicId ? FaqTopic::find($topicId)?->title : 'Unknown Topic';

        return [
            Placeholder::make('topic_name')
                ->label('Topic')
                ->content($topicName),
            TextInput::make('question')
                ->label('Question')
                ->placeholder('e.g, How do I cancel my booking?')
                ->required()
                ->maxLength(500),
            Textarea::make('answer')
                ->label('Answer')
                ->placeholder('Write a clear and helpful answer...')
                ->required()
                ->maxLength(2000)
                ->live(onBlur: false, debounce: 300)
                ->helperText(fn ($state): string => 'Character Limit: '.strlen($state ?? '').' / 2000')
                ->rows(5),
        ];
    }
}
