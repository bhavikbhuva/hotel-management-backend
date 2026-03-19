<x-filament-panels::page>
    {{-- Accordion sections --}}
    <div class="space-y-4" x-data="{ openSection: 'key_highlights' }">

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- WHO WE ARE SECTION --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

            {{-- Section Header --}}
            <div
                class="flex cursor-pointer items-center gap-4 px-5 py-4"
                x-on:click="openSection = openSection === 'who_we_are' ? null : 'who_we_are'"
            >
                {{-- Icon --}}
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 transition-colors duration-200 dark:bg-gray-800"
                    x-bind:style="openSection === 'who_we_are' ? 'background-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-user
                        class="h-5 w-5 text-blue-500 transition-colors duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'who_we_are' ? '!text-white' : ''"
                    />
                </div>

                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">Who we are Section</h4>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        Update the content that introduces your property and its identity to guests.
                    </p>
                </div>

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
                    x-bind:style="openSection === 'who_we_are' ? 'background-color: #2563eb; border-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-chevron-down
                        class="h-4 w-4 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'who_we_are' ? 'rotate-180 !text-white' : ''"
                    />
                </div>
            </div>

            {{-- Form Content --}}
            <div x-show="openSection === 'who_we_are'" x-collapse>
                <div class="border-t border-gray-200 px-5 py-6 dark:border-gray-700">
                    <form wire:submit="saveWhoWeAre">
                        <div class="space-y-4">

                            <div>
                                <label for="who_title" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Section Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="who_title"
                                    wire:model="who_title"
                                    placeholder="e.g. Search your stay"
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                @error('who_title')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="who_short_description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Short Description <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="who_short_description"
                                    wire:model="who_short_description"
                                    placeholder="Enter Short Description here"
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                @error('who_short_description')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="who_content" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Section Content <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="who_content"
                                    wire:model="who_content"
                                    rows="6"
                                    placeholder="Enter section content here..."
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                                @error('who_content')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Image Upload --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Section Image <span class="text-red-500">*</span>
                                </label>

                                <div class="mt-1 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-8 dark:border-gray-600">
                                    <div class="text-center">
                                        {{-- Custom file input --}}
                                        <div class="relative flex cursor-pointer flex-col items-center justify-center gap-2">
                                            <input
                                                type="file"
                                                id="whoImageUpload"
                                                wire:model="whoImageUpload"
                                                accept="image/png, image/svg+xml"
                                                class="absolute inset-0 z-50 m-0 h-full w-full cursor-pointer p-0 opacity-0 outline-none"
                                            />
                                            
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                <x-heroicon-o-plus class="h-5 w-5" />
                                            </div>
                                            
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                <span class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">Drag and Drop file here</span>
                                                or <span class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">Choose File</span>
                                            </div>
                                        </div>

                                        {{-- Loading state --}}
                                        <div wire:loading wire:target="whoImageUpload" class="mt-3 text-sm text-blue-500">
                                            Uploading...
                                        </div>

                                        {{-- Preview selected or saved image --}}
                                        @if ($whoImageUpload)
                                            <div class="mt-4 break-all text-sm text-gray-700 dark:text-gray-300">
                                                Selected: {{ $whoImageUpload->getClientOriginalName() }}
                                            </div>
                                        @elseif ($who_image)
                                            <div class="mt-4 flex flex-col items-center gap-2">
                                                <img src="{{ Storage::url($who_image) }}" alt="Preview" class="max-h-32 rounded-lg object-contain" />
                                                <span class="text-xs text-gray-500">Current Image</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <p>Maximum Size: 1620Px width and 600Px Hight</p>
                                    <p>Supported Files: PNG/SVG</p>
                                </div>
                                @error('whoImageUpload')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- Save Button --}}
                        <div class="mt-6">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                <span wire:loading.remove wire:target="saveWhoWeAre">Save Changes</span>
                                <span wire:loading wire:target="saveWhoWeAre">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- MANAGE KEY HIGHLIGHTS SECTION --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            {{-- Header --}}
            <div class="flex cursor-pointer items-center gap-4 px-5 py-4" x-on:click="openSection = openSection === 'key_highlights' ? null : 'key_highlights'">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 transition-colors duration-200 dark:bg-gray-800" x-bind:style="openSection === 'key_highlights' ? 'background-color: #2563eb;' : ''">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-blue-500 transition-colors duration-200 dark:text-gray-400" x-bind:class="openSection === 'key_highlights' ? '!text-white' : ''" />
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">Manage Key Highlights</h4>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Add up to 4 key points to showcase your property's strongest features.</p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-800" x-bind:style="openSection === 'key_highlights' ? 'background-color: #2563eb; border-color: #2563eb;' : ''">
                    <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-500 transition-transform duration-200 dark:text-gray-400" x-bind:class="openSection === 'key_highlights' ? 'rotate-180 !text-white' : ''" />
                </div>
            </div>

            {{-- Body --}}
            <div x-show="openSection === 'key_highlights'" x-collapse>
                <div class="border-t border-gray-200 px-5 py-6 dark:border-gray-700">
                    <form wire:submit="saveKeyHighlights">
                        <div class="space-y-4">
                            @foreach ($highlights as $index => $item)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-8 w-8 items-center justify-center rounded bg-gray-900 text-sm font-semibold text-white dark:bg-gray-800">{{ $index + 1 }}</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Point</span>
                                        </div>
                                        @if(count($highlights) > 1)
                                            <button type="button" wire:click="removeHighlight({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <x-heroicon-o-trash class="h-5 w-5" />
                                            </button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Highlight Text <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="highlights.{{ $index }}.title" placeholder="Enter Text Here.." class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                            @error('highlights.'.$index.'.title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="highlights.{{ $index }}.description" placeholder="Enter Description Here.." class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                            @error('highlights.'.$index.'.description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(count($highlights) < 4)
                                <button type="button" wire:click="addHighlight" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-500">
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-o-plus class="h-4 w-4" /> Add New Key Highlight
                                    </span>
                                </button>
                            @endif
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span wire:loading.remove wire:target="saveKeyHighlights">Save Changes</span>
                                <span wire:loading wire:target="saveKeyHighlights">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- OUR PROMISE SECTION --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            {{-- Header --}}
            <div class="flex cursor-pointer items-center gap-4 px-5 py-4" x-on:click="openSection = openSection === 'our_promise' ? null : 'our_promise'">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 transition-colors duration-200 dark:bg-gray-800" x-bind:style="openSection === 'our_promise' ? 'background-color: #2563eb;' : ''">
                    <x-heroicon-o-bars-3-bottom-left class="h-5 w-5 text-gray-500 transition-colors duration-200 dark:text-gray-400" x-bind:class="openSection === 'our_promise' ? '!text-white' : ''" />
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">Our Promise Section</h4>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Accepted forms of payment for bookings and services.</p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-800" x-bind:style="openSection === 'our_promise' ? 'background-color: #2563eb; border-color: #2563eb;' : ''">
                    <x-heroicon-o-chevron-down class="h-4 w-4 text-gray-500 transition-transform duration-200 dark:text-gray-400" x-bind:class="openSection === 'our_promise' ? 'rotate-180 !text-white' : ''" />
                </div>
            </div>

            {{-- Body --}}
            <div x-show="openSection === 'our_promise'" x-collapse>
                <div class="border-t border-gray-200 px-5 py-6 dark:border-gray-700">
                    <form wire:submit="saveOurPromise">
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Section Title <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="promise_title" placeholder="e.g. Search your stay" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                @error('promise_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Section Content <span class="text-red-500">*</span></label>
                                <textarea wire:model="promise_content" rows="4" placeholder="Enter section content here..." class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                                @error('promise_content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
                                <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Key Features (bullet Points)</label>
                                <div class="space-y-3">
                                    @foreach ($promise_features as $index => $feature)
                                        <div class="relative flex items-center">
                                            <input type="text" wire:model="promise_features.{{ $index }}" placeholder="Feature {{ $index + 1 }}" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 pr-10 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                            @if(count($promise_features) > 1)
                                                <button type="button" wire:click="removeFeature({{ $index }})" class="absolute right-2 p-1 text-gray-400 hover:text-red-500">
                                                    <x-heroicon-o-trash class="h-5 w-5" />
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="flex items-center gap-1 text-xs font-medium text-red-500">
                                        <x-heroicon-o-information-circle class="h-4 w-4" /> Maximum 4 key Points allowed.
                                    </p>
                                    @if(count($promise_features) < 4)
                                        <button type="button" wire:click="addFeature" class="text-sm font-medium text-blue-600 hover:text-blue-500 text-right">+ Add Feature</button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span wire:loading.remove wire:target="saveOurPromise">Save Changes</span>
                                <span wire:loading wire:target="saveOurPromise">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
