<div class="fi-topbar-ctn">
    @php
        $hasNavigation = filament()->hasNavigation();
    @endphp

    <nav class="fi-topbar">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_START) }}

        {{-- Mobile sidebar toggle --}}
        @if ($hasNavigation)
            <x-filament::icon-button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::OutlinedBars3"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen"
                class="fi-topbar-open-sidebar-btn"
            />

            <x-filament::icon-button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::OutlinedXMark"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.close()"
                x-show="$store.sidebar.isOpen"
                class="fi-topbar-close-sidebar-btn"
            />
        @endif

        <div class="fi-topbar-start">
            {{-- Back button --}}
            <button
                type="button"
                onclick="window.history.back()"
                class="fi-icon-btn fi-color-custom fi-color-gray fi-size-md relative flex items-center justify-center rounded-lg outline-none transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5 -ms-1.5"
            >
                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Branch/Property selector (hidden when no branches) --}}
            {{-- Placeholder: will be implemented when branches exist --}}
        </div>

        {{-- Right side --}}
        <div class="fi-topbar-end">
            {{-- Country switcher --}}
            @if ($this->currentCountry)
                <div x-data="{ open: false }" class="relative">
                    <button
                        type="button"
                        x-on:click="open = !open"
                        class="flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        <img
                            src="/assets/flags/{{ strtolower($this->currentCountry->iso_code) }}.svg"
                            alt="{{ $this->currentCountry->name }}"
                            class="h-4 w-6 rounded-sm object-cover"
                        />
                        <span>{{ $this->currentCountry->name }}</span>
                        <svg class="h-4 w-4 text-gray-400 transition" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        x-on:click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                    >
                        @foreach ($this->operatingCountries as $country)
                            <button
                                type="button"
                                wire:click="switchCountry({{ $country->id }})"
                                class="flex w-full items-center gap-x-2 px-3 py-2 text-sm transition hover:bg-gray-50 dark:hover:bg-gray-700 {{ $country->id === $this->currentCountry->id ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}"
                            >
                                <img
                                    src="/assets/flags/{{ strtolower($country->iso_code) }}.svg"
                                    alt="{{ $country->name }}"
                                    class="h-4 w-6 rounded-sm object-cover"
                                />
                                <span>{{ $country->name }}</span>
                                @if ($country->id === $this->currentCountry->id)
                                    <svg class="ms-auto h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Notification bell (placeholder) --}}
            <button
                type="button"
                class="fi-icon-btn relative flex items-center justify-center rounded-lg p-2 outline-none transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5"
            >
                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </button>

            {{-- User menu --}}
            @if (filament()->auth()->check())
                @php
                    $user = filament()->auth()->user();
                    $menuItems = $this->getUserMenuItems();

                    $itemsBeforeAndAfterThemeSwitcher = collect($menuItems)
                        ->groupBy(fn (\Filament\Actions\Action $item): bool => $item->getSort() < 0, preserveKeys: true)
                        ->all();
                    $itemsBeforeThemeSwitcher = $itemsBeforeAndAfterThemeSwitcher[true] ?? collect();
                    $itemsAfterThemeSwitcher = $itemsBeforeAndAfterThemeSwitcher[false] ?? collect();
                @endphp

                <x-filament::dropdown placement="bottom-end" teleport class="fi-user-menu">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="flex items-center gap-x-3 rounded-lg px-2 py-1.5 text-start transition hover:bg-gray-50 dark:hover:bg-white/5"
                        >
                            <x-filament-panels::avatar.user :user="$user" />

                            <div class="hidden lg:block">
                                <p class="text-sm font-semibold leading-tight text-gray-900 dark:text-white">
                                    {{ filament()->getUserName($user) }}
                                </p>
                                <p class="text-xs uppercase leading-tight text-gray-500 dark:text-gray-400">
                                    {{ $user->role->label() }}
                                </p>
                            </div>

                            <svg class="hidden h-4 w-4 text-gray-400 lg:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </x-slot>

                    @if ($itemsBeforeThemeSwitcher->isNotEmpty())
                        <x-filament::dropdown.list>
                            @foreach ($itemsBeforeThemeSwitcher as $key => $item)
                                {{ $item }}
                            @endforeach
                        </x-filament::dropdown.list>
                    @endif

                    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
                        <x-filament::dropdown.list>
                            <x-filament-panels::theme-switcher />
                        </x-filament::dropdown.list>
                    @endif

                    @if ($itemsAfterThemeSwitcher->isNotEmpty())
                        <x-filament::dropdown.list>
                            @foreach ($itemsAfterThemeSwitcher as $key => $item)
                                {{ $item }}
                            @endforeach
                        </x-filament::dropdown.list>
                    @endif
                </x-filament::dropdown>
            @endif
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_END) }}
    </nav>

    <x-filament-actions::modals />
</div>
