<x-filament-panels::page>
    {{-- Accordion sections --}}
    <div class="space-y-4" x-data="{ openSection: null }">

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- ABOUT US SECTION --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

            {{-- Section Header --}}
            <div
                class="flex cursor-pointer items-center gap-4 px-5 py-4"
                x-on:click="openSection = openSection === 'about_us' ? null : 'about_us'"
            >
                {{-- Icon --}}
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 transition-colors duration-200 dark:bg-gray-800"
                    x-bind:style="openSection === 'about_us' ? 'background-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-document-text
                        class="h-5 w-5 text-gray-500 transition-colors duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'about_us' ? '!text-white' : ''"
                    />
                </div>

                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">{{ __('admin.about_us') }}</h4>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('admin.manage_the_introduction_content_that_defines_your_brand_and_experience') }}
                    </p>
                </div>

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
                    x-bind:style="openSection === 'about_us' ? 'background-color: #2563eb; border-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-chevron-down
                        class="h-4 w-4 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'about_us' ? 'rotate-180 !text-white' : ''"
                    />
                </div>
            </div>

            {{-- Form Content --}}
            <div x-show="openSection === 'about_us'" x-collapse>
                <div class="border-t border-gray-200 px-5 py-6 dark:border-gray-700">
                    <form wire:submit="saveAboutUs">
                        <div class="space-y-4">

                            <div>
                                <label for="about_title" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Section Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="about_title"
                                    wire:model="about_title"
                                    placeholder="e.g. Search your stay"
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                @error('about_title')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="about_description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Section Description <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="about_description"
                                    wire:model="about_description"
                                    rows="4"
                                    placeholder="Brief description of this property type..."
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                                @error('about_description')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="about_button_text" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Button Text <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="about_button_text"
                                        wire:model="about_button_text"
                                        placeholder="Enter Button Text"
                                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                    @error('about_button_text')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="about_contact_no" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Contact No. <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="about_contact_no"
                                        wire:model="about_contact_no"
                                        placeholder="Enter Contact Number"
                                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                    @error('about_contact_no')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Image Upload —- direct file picker --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Enter Image Here <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="file"
                                    id="about_image_input"
                                    wire:model="aboutImageUpload"
                                    accept=".png,.svg"
                                    class="hidden"
                                />

                                @if ($aboutImageUpload)
                                    <div class="mb-3 w-1/2 overflow-hidden rounded-lg border border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-gray-800">
                                        <img src="{{ $aboutImageUpload->temporaryUrl() }}" alt="Image preview" class="max-h-40 w-full object-contain" />
                                        <div class="flex items-center justify-between border-t border-blue-200 px-3 py-2 dark:border-blue-700">
                                            <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('admin.new_image_selected_click_save_changes_to_apply') }}</p>
                                            <button type="button" onclick="document.getElementById('about_image_input').click()" class="text-xs font-medium text-gray-600 underline hover:text-gray-800 dark:text-gray-400">{{ __('admin.change') }}</button>
                                        </div>
                                    </div>
                                @elseif ($about_image)
                                    <div class="mb-3 w-1/2 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                                        <img src="{{ asset('storage/' . $about_image) }}" alt="About Us Image" class="max-h-40 w-full object-contain" />
                                        <div class="flex items-center justify-between border-t border-gray-200 px-3 py-2 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.current_image') }}</p>
                                            <button type="button" onclick="document.getElementById('about_image_input').click()" class="text-xs font-medium text-blue-600 underline hover:text-blue-800 dark:text-blue-400">{{ __('admin.change_image') }}</button>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 py-8 transition hover:border-blue-400 hover:bg-blue-50 dark:border-gray-600 dark:bg-gray-800"
                                        onclick="document.getElementById('about_image_input').click()"
                                    >
                                        <x-heroicon-o-arrow-up-tray class="mb-2 h-7 w-7 text-gray-400" />
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('admin.click_to_upload_image') }}</p>
                                        <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ __('admin.png_or_svg_max_2mb') }}</p>
                                    </div>
                                @endif

                                @error('aboutImageUpload')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                <div class="mt-1 flex justify-between text-xs text-gray-400 dark:text-gray-500">
                                    <span>{{ __('admin.maximum_size_770px_width_and_600px_height') }}</span>
                                    <span>{{ __('admin.supported_files_pngsvg') }}</span>
                                </div>
                            </div>

                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                {{ __('admin.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- AMENITIES & FACILITIES SECTION --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">

            {{-- Section Header --}}
            <div
                class="flex cursor-pointer items-center gap-4 px-5 py-4"
                x-on:click="openSection = openSection === 'amenities' ? null : 'amenities'"
            >
                {{-- Icon --}}
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 transition-colors duration-200 dark:bg-gray-800"
                    x-bind:style="openSection === 'amenities' ? 'background-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-squares-2x2
                        class="h-5 w-5 text-gray-500 transition-colors duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'amenities' ? '!text-white' : ''"
                    />
                </div>

                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">{{ __('admin.amenities_amp_facilities') }}</h4>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('admin.choose_which_amenities_should_appear_as_featured_highlights_for_guests') }}
                    </p>
                </div>

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
                    x-bind:style="openSection === 'amenities' ? 'background-color: #2563eb; border-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-chevron-down
                        class="h-4 w-4 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'amenities' ? 'rotate-180 !text-white' : ''"
                    />
                </div>
            </div>

            {{-- Form Content --}}
            <div x-show="openSection === 'amenities'" x-collapse>
                <div class="border-t border-gray-200 px-5 py-6 dark:border-gray-700">
                    <form wire:submit="saveAmenities">
                        <div class="space-y-5">

                            {{-- Facilities Searchable Multi-Select --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Facilities <span class="text-red-500">*</span>
                                </label>

                                @php
                                    $facilitiesForJs = $facilities->map(fn ($f) => [
                                        'id'       => $f->id,
                                        'name'     => $f->name,
                                        'category' => $f->category?->name ?? 'Other',
                                    ])->values()->toArray();
                                    $selectedForJs = array_values(array_map('intval', $amenities_selected));
                                @endphp

                                <div
                                    x-data="{
                                        open: false,
                                        search: '',
                                        selected: $wire.entangle('amenities_selected').live,
                                        options: @js($facilitiesForJs),
                                        get filtered() {
                                            const q = this.search.toLowerCase();
                                            return q
                                                ? this.options.filter(o => o.name.toLowerCase().includes(q))
                                                : this.options;
                                        },
                                        get groupedFiltered() {
                                            const groups = {};
                                            this.filtered.forEach(o => {
                                                if (!groups[o.category]) groups[o.category] = [];
                                                groups[o.category].push(o);
                                            });
                                            return groups;
                                        },
                                        toggle(id) {
                                            let items = [...this.selected];
                                            const idx = items.indexOf(id);
                                            if (idx >= 0) {
                                                items.splice(idx, 1);
                                            } else {
                                                items.push(id);
                                            }
                                            this.selected = items;
                                        },
                                        remove(id) {
                                            this.selected = this.selected.filter(s => s !== id);
                                        },
                                        getName(id) {
                                            return this.options.find(o => o.id === id)?.name ?? '';
                                        },
                                        isSelected(id) {
                                            return this.selected.includes(id);
                                        }
                                    }"
                                    x-on:click.outside="open = false; search = ''"
                                    class="relative"
                                >
                                    {{-- Input box with chips --}}
                                    <div
                                        @click="open = !open"
                                        class="flex min-h-[44px] cursor-pointer flex-wrap items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm transition focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                    >
                                        <template x-if="selected.length === 0">
                                            <span class="text-sm text-gray-400 dark:text-gray-500">{{ __('admin.select_facilities') }}</span>
                                        </template>

                                        <template x-for="id in selected" :key="id">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-sm font-medium text-gray-700 dark:bg-gray-600 dark:text-gray-200">
                                                <span x-text="getName(id)"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="remove(id)"
                                                    class="ml-0.5 rounded text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white"
                                                >
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        {{-- Chevron --}}
                                        <svg class="ml-auto h-4 w-4 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    {{-- Dropdown panel --}}
                                    <div
                                        x-show="open"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
                                        style="max-height: 260px; overflow-y: auto;"
                                    >
                                        {{-- Search box --}}
                                        <div class="sticky top-0 border-b border-gray-100 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                                            <input
                                                type="text"
                                                x-model="search"
                                                placeholder="Search..."
                                                @click.stop
                                                class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-900 focus:border-blue-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>

                                        {{-- Grouped options --}}
                                        <template x-for="[category, items] in Object.entries(groupedFiltered)" :key="category">
                                            <div>
                                                {{-- Category header --}}
                                                <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500" x-text="category"></div>

                                                {{-- Options --}}
                                                <template x-for="option in items" :key="option.id">
                                                    <button
                                                        type="button"
                                                        @click.stop="toggle(option.id)"
                                                        class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-left transition hover:bg-gray-50 dark:hover:bg-gray-700"
                                                        :class="isSelected(option.id) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200'"
                                                    >
                                                        {{-- Checkmark --}}
                                                        <svg
                                                            x-show="isSelected(option.id)"
                                                            class="h-4 w-4 shrink-0 text-blue-500"
                                                            viewBox="0 0 20 20" fill="currentColor"
                                                        >
                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                        </svg>
                                                        <svg
                                                            x-show="!isSelected(option.id)"
                                                            class="h-4 w-4 shrink-0 opacity-0"
                                                            viewBox="0 0 20 20" fill="currentColor"
                                                        >
                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span x-text="option.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="Object.keys(groupedFiltered).length === 0">
                                            <p class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500">{{ __('admin.no_facilities_found') }}</p>
                                        </template>
                                    </div>

                                </div>

                                @error('amenities_selected')
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Per-facility Description Cards --}}
                            @php
                                $selectedIds = collect($amenities_selected)->map(fn($id) => (int) $id);
                                $facilitiesMap = $facilities->keyBy('id');
                            @endphp

                            @if ($selectedIds->isNotEmpty())
                                <div class="space-y-4">
                                    @foreach ($selectedIds as $facilityId)
                                        @php $facility = $facilitiesMap->get($facilityId) @endphp
                                        @if ($facility)
                                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                                                {{-- Facility Name + Icon --}}
                                                <div class="mb-3 flex items-center gap-2">
                                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                                        @if ($facility->icon)
                                                            <img src="{{ $facility->icon }}" alt="{{ $facility->name }}" class="h-5 w-5 object-contain" />
                                                        @else
                                                            <x-heroicon-o-squares-2x2 class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                                        @endif
                                                    </div>
                                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $facility->name }}</span>
                                                </div>

                                                {{-- Description textarea --}}
                                                <div>
                                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                                                        Description <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea
                                                        wire:model="amenities_descriptions.{{ $facilityId }}"
                                                        rows="3"
                                                        placeholder="Enter Description Here..."
                                                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                    ></textarea>
                                                    @error("amenities_descriptions.{$facilityId}")
                                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                {{ __('admin.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- GUEST REVIEWS SECTION --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div
                class="flex cursor-pointer items-center gap-4 px-5 py-4"
                x-on:click="openSection = openSection === 'guest_reviews' ? null : 'guest_reviews'"
            >
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 transition-colors duration-200 dark:bg-gray-800"
                    x-bind:style="openSection === 'guest_reviews' ? 'background-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-star
                        class="h-5 w-5 text-gray-500 transition-colors duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'guest_reviews' ? '!text-white' : ''"
                    />
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">{{ __('admin.guest_reviews') }}</h4>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('admin.control_which_guest_reviews_are_highlighted_on_your_homepage') }}
                    </p>
                </div>
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
                    x-bind:style="openSection === 'guest_reviews' ? 'background-color: #2563eb; border-color: #2563eb;' : ''"
                >
                    <x-heroicon-o-chevron-down
                        class="h-4 w-4 text-gray-500 transition-transform duration-200 dark:text-gray-400"
                        x-bind:class="openSection === 'guest_reviews' ? 'rotate-180 !text-white' : ''"
                    />
                </div>
            </div>

            <div x-show="openSection === 'guest_reviews'" x-collapse>
                <div class="border-t border-gray-200 px-5 py-6 dark:border-gray-700">
                    <form wire:submit="saveReviews">
                        <div class="space-y-5" x-data="{ 
                            removeReview(id) {
                                let selected = $wire.reviews_selected;
                                $wire.$set('reviews_selected', selected.filter(i => i != id));
                            }
                        }">
                            
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @foreach($this->selectedReviewsModels as $review)
                                    <div class="relative flex flex-col justify-between rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center gap-3">
                                                @if($review->user?->avatar)
                                                    <img src="{{ url($review->user->avatar) }}" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100/50 text-blue-600 font-bold dark:bg-blue-900/50 dark:text-blue-400">
                                                        {{ substr($review->user?->name ?? 'U', 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $review->user?->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->user?->name ? 'Guest' : 'Anonymous' }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-1">
                                                    <x-heroicon-s-star class="h-4 w-4 text-amber-500"/>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($review->rating, 1) }}</span>
                                                </div>
                                                <button type="button" @click="removeReview({{ $review->id }})" class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 text-gray-400 transition hover:bg-gray-100 hover:text-red-500 dark:border-gray-600 dark:hover:bg-gray-700">
                                                    <x-heroicon-o-trash class="h-4 w-4"/>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <p class="mt-4 text-sm text-gray-600 line-clamp-3 dark:text-gray-300">
                                            {{ $review->review }}
                                        </p>
                                    </div>
                                @endforeach

                                {{-- "Add Reviews" card — always the last cell in the grid --}}
                                <div 
                                    class="flex min-h-[8rem] cursor-pointer flex-col items-center justify-start rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 px-6 pt-5 pb-5 text-center transition hover:border-blue-400 hover:bg-blue-100/70 dark:border-gray-600 dark:bg-gray-800/50 dark:hover:border-gray-500 dark:hover:bg-gray-800"
                                    x-on:click="$dispatch('open-modal', { id: 'add-reviews-modal' })"
                                >
                                    <p class="mb-4 text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('admin.add_reviews_for_the_highlight_in_homepage') }}</p>
                                    <div class="inline-flex items-center gap-2 rounded-full bg-gray-900 px-5 py-2 text-sm font-medium text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                        Add Reviews
                                        <x-heroicon-o-plus-circle class="h-5 w-5"/>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                                {{ __('admin.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <x-filament::modal id="add-reviews-modal" width="4xl">
        <x-slot name="heading">
            Add Reviews
        </x-slot>

        <div class="mb-4 flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <label class="sr-only">{{ __('admin.search_reviews') }}</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="reviewSearch" 
                    placeholder="e.g., Search Reviews" 
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
            </div>
            <div class="w-full shrink-0 sm:w-40">
                <label class="sr-only">{{ __('admin.filter_by_stars') }}</label>
                <select 
                    wire:model.live="reviewStarsFilter"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
                    <option value="">{{ __('admin.all_ratings') }}</option>
                    <option value="5">{{ __('admin.5_stars') }}</option>
                    <option value="4">{{ __('admin.4_stars') }}</option>
                    <option value="3">{{ __('admin.3_stars') }}</option>
                    <option value="2">{{ __('admin.2_stars') }}</option>
                    <option value="1">{{ __('admin.1_star') }}</option>
                </select>
            </div>
        </div>

        <div class="h-[400px] overflow-y-auto pr-2" x-data="{ 
            staged: $wire.entangle('reviews_selected').live
        }">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($this->availableReviews as $availReview)
                    <div 
                        class="relative flex cursor-pointer flex-col justify-between rounded-xl border p-5 transition"
                        :class="staged.includes({{ $availReview->id }}) ? 'border-blue-500 bg-blue-50/30 dark:border-blue-500 dark:bg-blue-900/20' : 'border-gray-200 bg-white hover:border-blue-300 dark:border-gray-700 dark:bg-gray-800'"
                        @click="
                            const idx = staged.indexOf({{ $availReview->id }});
                            let temp = [...staged];
                            if (idx > -1) { temp.splice(idx, 1); } 
                            else { temp.push({{ $availReview->id }}); }
                            staged = temp;
                        "
                    >
                        <div class="absolute right-4 top-4">
                            <div 
                                class="flex h-5 w-5 items-center justify-center rounded border transition"
                                :class="staged.includes({{ $availReview->id }}) ? 'border-blue-600 bg-blue-600 dark:border-blue-500 dark:bg-blue-500' : 'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-700'"
                            >
                                <svg x-show="staged.includes({{ $availReview->id }})" class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div class="flex items-start justify-between pr-8">
                            <div class="flex items-center gap-3">
                                @if($availReview->user?->avatar)
                                    <img src="{{ url($availReview->user->avatar) }}" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100/50 text-blue-600 font-bold dark:bg-blue-900/50 dark:text-blue-400">
                                        {{ substr($availReview->user?->name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $availReview->user?->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $availReview->user?->name ? 'Guest' : 'Anonymous' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-xs text-gray-600 line-clamp-2 pr-4 flex-1 dark:text-gray-300">
                                {{ $availReview->review }}
                            </p>
                            <div class="flex shrink-0 items-center gap-1">
                                <x-heroicon-s-star class="h-4 w-4 text-amber-500"/>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($availReview->rating, 1) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 py-10 text-center text-sm text-gray-500 lg:col-span-2">
                        {{ __('admin.no_reviews_found_matching_your_search') }}
                    </div>
                @endforelse
            </div>
        </div>

        <x-slot name="footerActions">
            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'add-reviews-modal' })">
                Cancel
            </x-filament::button>
            <x-filament::button color="primary" x-on:click="$dispatch('close-modal', { id: 'add-reviews-modal' })">
                Add Reviews
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <x-filament-actions::modals />
</x-filament-panels::page>
