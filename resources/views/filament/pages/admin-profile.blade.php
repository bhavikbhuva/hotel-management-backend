<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Profile Card --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            {{-- Header: Avatar + Name + Edit Button --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <div class="flex items-center gap-x-4">
                    @if ($user->avatar)
                        <img
                            src="{{ str_starts_with($user->avatar, 'avatars/') ? asset('storage/' . $user->avatar) : asset('avatars/defaultUser.svg') }}"
                            alt="{{ $user->name }}"
                            class="h-12 w-12 rounded-full object-cover"
                        />
                    @else
                        <img
                            src="{{ asset('avatars/defaultUser.svg') }}"
                            alt="{{ $user->name }}"
                            class="h-12 w-12 rounded-full object-cover"
                        />
                    @endif
                    <span class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </span>
                </div>

                {{ $this->editProfileAction }}
            </div>

            {{-- Contact Info: Phone + Email --}}
            <div class="flex flex-wrap gap-8 px-6 py-4">
                {{-- Phone --}}
                <div class="flex items-center gap-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.phone') }}</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $user->phone ?? __('admin.not_set') }}
                        </p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-center gap-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.email') }}</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password Card --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('admin.change_password') }}</h3>
            </div>

            <form wire:submit="updatePassword" class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('admin.current_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="current_password"
                                wire:model="current_password"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="******"
                            />
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="new_password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('admin.new_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="new_password"
                                wire:model="new_password"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="******"
                            />
                        </div>
                        @error('new_password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm New Password --}}
                    <div>
                        <label for="new_password_confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('admin.confirm_password') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="new_password_confirmation"
                                wire:model="new_password_confirmation"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="******"
                            />
                        </div>
                        @error('new_password_confirmation')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        {{ __('admin.update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
