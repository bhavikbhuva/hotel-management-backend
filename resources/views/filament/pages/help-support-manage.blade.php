<x-filament-panels::page>
    {{-- Tab Switcher --}}
    <div class="flex items-center justify-between">
        <div class="inline-flex gap-1 rounded-xl p-1 dark:bg-gray-800" style="background-color: #dbeafe;">
            <button
                wire:click="switchTab('how-it-works')"
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition"
                style="{{ $currentTab === 'how-it-works' ? 'background-color: #2563eb; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.1);' : 'color: #4b5563;' }}"
            >
                <x-heroicon-o-list-bullet class="h-4 w-4" />
                How It Works
            </button>

            <button
                wire:click="switchTab('faqs')"
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition"
                style="{{ $currentTab === 'faqs' ? 'background-color: #2563eb; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.1);' : 'color: #4b5563;' }}"
            >
                <x-heroicon-o-question-mark-circle class="h-4 w-4" />
                Topics & FAQs
            </button>
        </div>

        @if ($currentTab === 'how-it-works' && $this->canAddStep())
            {{ $this->addStepAction }}
        @endif
    </div>

    {{-- Tab Content --}}
    @if ($currentTab === 'how-it-works')
        @if ($this->getHasSteps())
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                @foreach ($this->getSteps() as $step)
                    <div class="step-card relative rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-primary-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:hover:border-primary-600">
                        {{-- Hover Actions --}}
                        <div class="step-card-actions absolute right-3 top-3 items-center gap-1">
                            {{ ($this->editStepAction)(['step' => $step->id]) }}
                            {{ ($this->deleteStepAction)(['step' => $step->id]) }}
                        </div>

                        {{-- Step Number --}}
                        <span class="mb-4 inline-flex items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white dark:bg-white dark:text-gray-900" style="width: 36px; height: 36px; min-width: 36px;">
                            {{ str_pad($step->sort_order, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        {{-- Content --}}
                        <h4 class="mt-3 text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $step->title }}
                        </h4>
                        <p class="mt-1 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                            {{ $step->description }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="fi-ta-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex w-full flex-col items-center justify-center gap-4 px-6 py-12 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <x-heroicon-o-list-bullet class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                    </div>

                    <h4 class="text-base font-semibold text-gray-950 dark:text-white">
                        No steps added yet
                    </h4>
                    <p class="max-w-md text-sm text-gray-500 dark:text-gray-400">
                        This section allows you to define step-by-step guidance shown to users
                        on how booking and stays work on the platform.
                    </p>
                </div>
            </div>
        @endif
    @else
        {{-- FAQs tab content will be added later --}}
        <div class="fi-ta-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex w-full flex-col items-center justify-center gap-4 px-6 py-12 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                    <x-heroicon-o-question-mark-circle class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                </div>

                <h4 class="text-base font-semibold text-gray-950 dark:text-white">
                    No topics or FAQs created
                </h4>
                <p class="max-w-md text-sm text-gray-500 dark:text-gray-400">
                    Create help topics and FAQs to answer common user questions related to
                    bookings, payments, cancellations, and account management.
                </p>
            </div>
        </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
