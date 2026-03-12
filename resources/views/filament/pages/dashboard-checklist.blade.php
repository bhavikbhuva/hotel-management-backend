<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Platform Setup Checklist</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Complete these essential steps to fully configure your platform.
                </p>
            </div>

            {{-- Progress --}}
            <div class="flex flex-col items-end gap-y-1">
                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                    Progress
                    <span class="ms-2">{{ round(($completedCount / $totalTasks) * 100) }}%</span>
                </span>
                <div class="h-2 w-40 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                    <div
                        class="h-full rounded-full bg-blue-600 transition-all duration-300"
                        style="width: {{ ($completedCount / $totalTasks) * 100 }}%"
                    ></div>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $completedCount }} of {{ $totalTasks }} tasks completed
                </span>
            </div>
        </div>

        {{-- Checklist cards grid --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($checklistTasks as $task)
                <div class="relative rounded-xl border bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 {{ $task['isCompleted'] ? 'border-green-200 dark:border-green-800' : 'border-gray-200' }}">
                    {{-- Icon --}}
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg {{ $task['isCompleted'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        @if ($task['isCompleted'])
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        @else
                            @switch($task['key'])
                                @case('admin_profile')
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    @break
                                @case('cities')
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" />
                                    </svg>
                                    @break
                                @case('taxes')
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                                    </svg>
                                    @break
                                @case('cancellation_policy')
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                                    </svg>
                                    @break
                                @case('legal_policy')
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                    </svg>
                                    @break
                            @endswitch
                        @endif
                    </div>

                    {{-- Title & Description --}}
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $task['label'] }}
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $task['description'] }}
                    </p>

                    {{-- CTA Button --}}
                    <div class="mt-4">
                        @if ($task['isCompleted'])
                            <span class="inline-flex items-center gap-x-1 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-400">
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Completed
                            </span>
                        @else
                            <a
                                href="{{ $task['route'] }}"
                                class="inline-flex items-center gap-x-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-blue-700"
                            >
                                {{ $task['buttonLabel'] }}
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
