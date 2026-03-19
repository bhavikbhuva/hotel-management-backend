<x-filament-panels::page>

    <div class="grid grid-cols-1 gap-6" x-data="{ ruleToDelete: null }">

        {{-- CUTOFF TIME CARD --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 overflow-hidden">
            <div class="bg-blue-50/50 px-6 py-4 border-b border-gray-200 dark:bg-blue-900/10 dark:border-gray-700 flex items-center gap-3">
                <div class="rounded-lg bg-blue-100 p-2 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <x-heroicon-o-clock class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Global Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Set the universal cancellation cutoff time for this country.</p>
                </div>
            </div>
            
            <div class="p-6">
                <form wire:submit="saveCutoffTime" class="flex items-end gap-4 max-w-sm">
                    <div class="flex-1">
                        <label for="cutoff_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cancellation Cutoff Time <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="time" 
                            id="cutoff_time" 
                            wire:model="cutoff_time" 
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        @error('cutoff_time') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Save Time
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- CANCELLATION RULES CARD --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-800/50 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-2 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                        <x-heroicon-o-no-symbol class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Cancellation Rules</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rules regarding booking cancellations, refunds, and no-shows.</p>
                    </div>
                </div>
                
                @if(!$showForm)
                    <button wire:click="addNewRule" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <x-heroicon-o-plus class="h-4 w-4" /> Add Rule
                    </button>
                @endif
            </div>

            <div class="p-6 space-y-4 bg-gray-50/50 dark:bg-gray-900/50">
                
                {{-- Listed Rules (and Inline Editing) --}}
                @foreach($rulesList as $index => $ruleItem)
                    @if($showForm && $editingRuleId === $ruleItem['id'])
                        {{-- INLINE EDIT FORMAT --}}
                        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-3 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-gray-900 text-sm font-bold text-white dark:bg-gray-700">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">Rule</span>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="cancelRule" class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 shadow-sm transition hover:bg-red-50 hover:text-red-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-red-400" title="Cancel">
                                        <x-heroicon-o-trash class="h-5 w-5" />
                                    </button>
                                    <button type="button" wire:click="saveRule" class="rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                        Save Rule
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Edit Input Fields Partial --}}
                            @include('filament.pages.partials.cancellation-rule-form')
                        </div>
                    @else
                        {{-- DISPLAY CARD --}}
                        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-gray-900 text-sm font-bold text-white dark:bg-gray-700">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">Cancellation Period</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $ruleItem['days_before_checkin'] }} Days Before Check-in</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xs tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                        {{ $ruleItem['refund_percentage'] > 0 ? 'Refundable' : 'Non - Refundable' }}
                                    </p>
                                    @if($ruleItem['refund_percentage'] > 0)
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $ruleItem['refund_percentage'] }}%</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 border-l border-gray-200 pl-4 dark:border-gray-700">
                                    <button type="button" wire:click="editRule({{ $ruleItem['id'] }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-50 hover:text-blue-600 dark:hover:bg-gray-700 dark:hover:text-blue-400">
                                        <x-heroicon-o-pencil class="h-5 w-5" />
                                    </button>
                                    <button type="button" @click="ruleToDelete = {{ $ruleItem['id'] }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-50 hover:text-red-600 dark:hover:bg-gray-700 dark:hover:text-red-400">
                                        <x-heroicon-o-trash class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- NEW RULE APPEND FORM --}}
                @if($showForm && $editingRuleId === null)
                    <div class="rounded-xl border border-gray-200 bg-white p-5 mt-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-3 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-gray-900 text-sm font-bold text-white dark:bg-gray-700">
                                    {{ count($rulesList) + 1 }}
                                </div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Rule</span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="cancelRule" class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 shadow-sm transition hover:bg-red-50 hover:text-red-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-red-400" title="Cancel">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                                <button type="button" wire:click="saveRule" class="rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    Save Rule
                                </button>
                            </div>
                        </div>

                        {{-- Edit Input Fields Partial --}}
                        @include('filament.pages.partials.cancellation-rule-form')
                    </div>
                @endif
                
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-cloak x-show="ruleToDelete !== null" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="ruleToDelete !== null" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity dark:bg-gray-900/80"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="ruleToDelete !== null" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         @click.away="ruleToDelete = null"
                         class="relative transform overflow-hidden rounded-2xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:p-6 dark:bg-gray-800 dark:ring-1 dark:ring-white/10">
                        
                        <div>
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/20">
                                <x-heroicon-o-trash class="h-6 w-6 text-red-600 dark:text-red-500" />
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">Delete Cancellation Policy Rule?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete? This rule will no longer apply to any bookings.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button type="button" @click="ruleToDelete = null" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700 transition">
                                Cancel
                            </button>
                            <button type="button" x-on:click="$wire.deleteRule(ruleToDelete).then(() => { ruleToDelete = null; })" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:col-start-2 transition">
                                Yes, Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
