<div class="min-h-screen">

    {{-- ═══════════════════════════════════════════════════════════════════════
         STEP 1 — Admin Account (centered card layout)
         ═══════════════════════════════════════════════════════════════════════ --}}
    @if ($currentStep === 1)
        <div class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 py-12">

            {{-- Logo --}}
            <div class="mb-8">
                <div class="h-8 w-36 rounded bg-blue-200"></div>
            </div>

            <div class="w-full max-w-md">

                {{-- Heading --}}
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">BookingHub Setup</h1>
                    <p class="mt-2 text-sm text-gray-500">Create your admin account to get started.</p>
                </div>

                {{-- Form Card --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                    <div class="space-y-5">

                        {{-- Name --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <input wire:model="name" type="text" placeholder="John Doe"
                                    class="block w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}" />
                            </div>
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <input wire:model="email" type="email" placeholder="admin@example.com"
                                    class="block w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}" />
                            </div>
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Phone (optional) --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Phone <span class="font-normal text-gray-400">(optional)</span>
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 6.75Z" />
                                    </svg>
                                </div>
                                <input wire:model="phone" type="tel" placeholder="+1 234 567 8900"
                                    class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <input wire:model="password" type="password" placeholder="Min. 8 characters"
                                    class="block w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}" />
                            </div>
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Confirm Password</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <input wire:model="passwordConfirmation" type="password" placeholder="Repeat your password"
                                    class="block w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('passwordConfirmation') ? 'border-red-400' : 'border-gray-300' }}" />
                            </div>
                            @error('passwordConfirmation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- Next Button --}}
                    <div class="mt-6">
                        <button wire:click="nextStep" wire:loading.attr="disabled" type="button"
                            class="flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                            <span wire:loading.remove wire:target="nextStep">Next: System Mode &rarr;</span>
                            <span wire:loading wire:target="nextStep">Processing&hellip;</span>
                        </button>
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} BookingHub. All rights reserved.</p>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         STEPS 2+ — Two-column layout (sidebar + content)
         ═══════════════════════════════════════════════════════════════════════ --}}
    @else
        <div class="flex h-screen bg-white">

            {{-- ── Left Sidebar ─────────────────────────────────────────── --}}
            <div class="flex w-56 flex-shrink-0 flex-col border-r border-gray-200">

                {{-- Logo --}}
                <div class="px-5 pb-4 pt-5">
                    <div class="h-8 w-36 rounded bg-blue-200"></div>
                </div>

                {{-- Sidebar Header --}}
                <div class="border-b border-gray-100 px-5 pb-5">
                    <h2 class="text-sm font-bold text-gray-900">Initial Setup</h2>
                    <p class="mt-1 text-xs leading-relaxed text-gray-500">Choose how you want to manage your properties.</p>
                </div>

                {{-- Step Navigation --}}
                @php
                    $configSteps = [
                        2 => ['name' => 'Setup Mode',     'description' => 'System Structure'],
                        3 => ['name' => 'Select Countries', 'description' => 'Operating Countries'],
                        4 => ['name' => 'Property Type',  'description' => 'Property Category'],
                    ];
                @endphp

                <nav class="flex-1 space-y-1 px-5 py-5">
                    @foreach ($configSteps as $stepNumber => $step)
                        @php
                            $isActive    = $currentStep === $stepNumber;
                            $isCompleted = $currentStep > $stepNumber;
                        @endphp
                        <div class="flex items-start gap-3 py-2">
                            {{-- Circle indicator --}}
                            <div class="mt-0.5 flex-shrink-0">
                                @if ($isCompleted)
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600">
                                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                @elseif ($isActive)
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600">
                                        <div class="h-2 w-2 rounded-full bg-white"></div>
                                    </div>
                                @else
                                    <div class="h-5 w-5 rounded-full border-2 border-gray-300"></div>
                                @endif
                            </div>
                            {{-- Step text --}}
                            <div>
                                <p class="text-xs {{ $isActive ? 'font-semibold text-gray-900' : 'font-medium text-gray-400' }}">
                                    {{ $step['name'] }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $step['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </nav>

                {{-- Sidebar Footer --}}
                <div class="px-5 py-4">
                    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} BookingHub. All rights reserved.</p>
                </div>
            </div>

            {{-- ── Right Content Area ───────────────────────────────────── --}}
            <div class="flex flex-1 flex-col overflow-hidden">

                {{-- Scrollable Content --}}
                <div class="flex-1 overflow-y-auto p-10">

                    {{-- ─── Step 2: System Mode ─────────────────────────── --}}
                    @if ($currentStep === 2)
                        <div class="w-full">

                            {{-- Step Title --}}
                            <h1 class="text-xl font-bold text-gray-900">Select System Mode</h1>
                            <p class="mt-1 text-sm text-gray-500">How will properties be managed on this platform?</p>

                            {{-- Warning Banner --}}
                            <div class="mt-6 flex gap-4 rounded-lg border border-red-100 bg-red-50 p-4">
                                <div class="flex-shrink-0">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-500">
                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Note</p>
                                    <p class="mt-0.5 text-sm text-gray-600">
                                        Once you select the <strong>Multi-Property Setup</strong>, this configuration cannot be changed later.
                                        You will not be able to switch back to the Single-Property Setup after completing the setup process.
                                    </p>
                                </div>
                            </div>

                            {{-- Mode Selection Cards --}}
                            <div class="mt-6 grid grid-cols-2 gap-5">

                                {{-- Multi-Property Card --}}
                                <div wire:click="$set('systemMode', 'multi')"
                                    class="cursor-pointer rounded-xl border p-5 transition-colors {{ $systemMode === 'multi' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200 hover:border-gray-300' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            {{-- Multi-Property Icon --}}
                                            <svg class="h-10 w-10 flex-shrink-0 text-gray-600" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="2" y="17" width="15" height="20" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                                <rect x="5" y="21" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="11" y="21" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="5" y="27" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="7" y="31" width="5" height="6" rx="0.5" fill="currentColor"/>
                                                <rect x="2" y="10" width="15" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                                <rect x="23" y="13" width="15" height="24" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                                <rect x="26" y="18" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="31" y="18" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="26" y="24" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="31" y="24" width="3.5" height="3.5" rx="0.5" fill="currentColor"/>
                                                <rect x="28" y="30" width="5" height="7" rx="0.5" fill="currentColor"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-900">Multi-Property Setup</span>
                                        </div>
                                        {{-- Radio Indicator --}}
                                        @if ($systemMode === 'multi')
                                            <div class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-600">
                                                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-5 w-5 flex-shrink-0 rounded-full border-2 border-gray-300"></div>
                                        @endif
                                    </div>
                                    <p class="mt-3 text-sm leading-relaxed text-gray-500">
                                        This setup is for platforms that allow multiple property owners or partners to list their properties. Admin reviews and approves partner properties, manages compliance, and earns revenue through a commission-based model. Best suited for hotel marketplaces and multi-owner platforms.
                                    </p>
                                </div>

                                {{-- Single-Property Card --}}
                                <div wire:click="$set('systemMode', 'single')"
                                    class="cursor-pointer rounded-xl border p-5 transition-colors {{ $systemMode === 'single' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200 hover:border-gray-300' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            {{-- Single-Property Icon --}}
                                            <svg class="h-10 w-10 flex-shrink-0 text-gray-600" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="7" y="14" width="26" height="23" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                                <path d="M7 14L20 6L33 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <rect x="12" y="19" width="5" height="5" rx="0.5" fill="currentColor"/>
                                                <rect x="23" y="19" width="5" height="5" rx="0.5" fill="currentColor"/>
                                                <rect x="12" y="27" width="5" height="5" rx="0.5" fill="currentColor"/>
                                                <rect x="23" y="27" width="5" height="5" rx="0.5" fill="currentColor"/>
                                                <rect x="17" y="30" width="6" height="7" rx="0.5" fill="currentColor"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-900">Single-Property Setup</span>
                                        </div>
                                        {{-- Radio Indicator --}}
                                        @if ($systemMode === 'single')
                                            <div class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-600">
                                                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-5 w-5 flex-shrink-0 rounded-full border-2 border-gray-300"></div>
                                        @endif
                                    </div>
                                    <p class="mt-3 text-sm leading-relaxed text-gray-500">
                                        This setup is for a single owner managing one brand with multiple branches across cities. Admin controls all operations, pricing, and bookings directly, with no partner or commission flow. Ideal for hotel chains or standalone businesses.
                                    </p>
                                </div>

                            </div>
                            {{-- End Mode Cards --}}

                        </div>
                    @endif
                    {{-- End Step 2 --}}

                    {{-- ─── Step 3: Select Country ──────────────────────── --}}
                    @if ($currentStep === 3)
                        <div class="w-full">

                            {{-- Step Title + Search Bar --}}
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h1 class="text-xl font-bold text-gray-900">Select Countries</h1>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Select the countries where your business operates.
                                        @if (count($selectedCountries) > 0)
                                            <span class="font-medium text-blue-600">{{ count($selectedCountries) }} selected</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-shrink-0 items-center gap-2">
                                    <input
                                        wire:model.live.debounce.300ms="countrySearch"
                                        type="text"
                                        placeholder="Search Country"
                                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    />
                                    <button type="button"
                                        class="flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                        Search
                                    </button>
                                </div>
                            </div>

                            {{-- Country Grid --}}
                            <div class="mt-6 grid grid-cols-4 gap-4">
                                @forelse ($countries as $country)
                                    @php $isSelected = in_array($country->id, $selectedCountries); @endphp
                                    <div
                                        wire:click="toggleCountry({{ $country->id }})"
                                        class="cursor-pointer rounded-lg border p-4 transition-colors {{ $isSelected ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200 hover:border-gray-300' }}"
                                    >
                                        {{-- Top row: flag + checkbox --}}
                                        <div class="flex items-start justify-between">
                                            <img
                                                src="/assets/flags/{{ $country->iso_code }}.svg"
                                                alt="{{ $country->name }}"
                                                class="h-12 w-12 rounded-full border border-gray-100 object-cover"
                                            />
                                            {{-- Checkbox indicator --}}
                                            @if ($isSelected)
                                                <div class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded border-2 border-blue-600 bg-blue-600">
                                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="h-5 w-5 flex-shrink-0 rounded border-2 border-gray-300"></div>
                                            @endif
                                        </div>

                                        {{-- Country name --}}
                                        <p class="mt-3 text-sm font-semibold text-gray-900">{{ $country->name }}</p>

                                        {{-- Currency --}}
                                        <p class="mt-0.5 truncate text-xs text-gray-400">
                                            {{ $country->currency_symbol ? $country->currency_symbol . ' ' : '' }}{{ $country->currency_code }} &mdash; {{ $country->currency_name }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="col-span-4 py-12 text-center text-sm text-gray-400">
                                        No countries found for "{{ $countrySearch }}".
                                    </div>
                                @endforelse
                            </div>

                            @error('selectedCountries')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>
                    @endif
                    {{-- End Step 3 --}}

                </div>
                {{-- End Scrollable Content --}}

                {{-- ── Footer Navigation ────────────────────────────────── --}}
                <div class="flex flex-shrink-0 items-center justify-between border-t border-gray-100 px-10 py-5">
                    <div></div>
                    <div class="flex items-center gap-3">
                        @if ($currentStep > 1)
                            <button wire:click="previousStep" type="button"
                                class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Back
                            </button>
                        @endif
                        <button wire:click="nextStep" wire:loading.attr="disabled" type="button"
                            class="flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60">
                            <span wire:loading.remove wire:target="nextStep">Next &rarr;</span>
                            <span wire:loading wire:target="nextStep">Processing&hellip;</span>
                        </button>
                    </div>
                </div>

            </div>
            {{-- End Right Content Area --}}

        </div>
    @endif

</div>
