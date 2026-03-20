@php
    $title = $get('title');
    $shortDescription = $get('short_description');
    $categoryId = $get('blog_category_id');
    $coverImage = $get('cover_image');

    $categoryName = $categoryId ? \App\Models\BlogCategory::find($categoryId)?->name : null;

    // Resolve existing stored image URL (edit mode)
    $storedImageUrl = null;
    if ($coverImage) {
        if (is_string($coverImage) && Storage::disk('public')->exists($coverImage)) {
            $storedImageUrl = Storage::disk('public')->url($coverImage);
        } elseif (is_array($coverImage)) {
            $firstFile = collect($coverImage)->first();
            if ($firstFile && is_string($firstFile) && Storage::disk('public')->exists($firstFile)) {
                $storedImageUrl = Storage::disk('public')->url($firstFile);
            }
        }
    }
@endphp

<div
    x-data="{ previewUrl: @js($storedImageUrl) }"
    x-on:file-pond-add-file.window="
        const file = $event.detail?.file;
        if (file && file.type?.startsWith('image/')) {
            previewUrl = URL.createObjectURL(file);
        }
    "
    x-on:file-pond-remove-file.window="previewUrl = @js($storedImageUrl)"
    x-init="
        // Listen for FilePond native events at document level
        document.addEventListener('FilePond:addfile', (e) => {
            const file = e.detail?.file?.file;
            if (file && file.type?.startsWith('image/')) {
                previewUrl = URL.createObjectURL(file);
            }
        });
        document.addEventListener('FilePond:removefile', () => {
            previewUrl = @js($storedImageUrl);
        });
    "
    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
>
    {{-- Cover Image --}}
    <div class="relative h-44 w-full bg-gray-100 dark:bg-gray-700">
        <template x-if="previewUrl">
            <img
                :src="previewUrl"
                alt="Cover preview"
                class="h-full w-full object-cover"
            />
        </template>
        <template x-if="! previewUrl">
            <div class="flex h-full w-full items-center justify-center text-xs text-gray-400 dark:text-gray-500">
                {{ __('admin.1620_x_823') }}
            </div>
        </template>

        {{-- Date Badge --}}
        <div class="absolute bottom-3 right-3 rounded-lg bg-white px-2.5 py-1.5 text-center shadow-sm dark:bg-gray-800">
            <div class="text-base font-bold leading-tight text-gray-900 dark:text-white">
                {{ now()->format('d') }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ now()->format('M') }}
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="space-y-2 p-4">
        {{-- Category --}}
        @if ($categoryName)
            <span class="text-xs font-medium text-primary-600 dark:text-primary-400">
                {{ $categoryName }}
            </span>
        @else
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ __('admin.category') }}</span>
        @endif

        {{-- Title --}}
        <h4 class="text-sm font-semibold leading-snug text-gray-900 dark:text-white">
            {{ $title ?: 'Blog Title Goes Here' }}
        </h4>

        {{-- Short Description --}}
        <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
            {{ $shortDescription ? Str::limit($shortDescription, 100) : 'A short summary of your blog post will appear here. Write something catchy to engage your readers...' }}
        </p>

        {{-- Read More --}}
        <div class="pt-1">
            <span class="inline-flex items-center gap-1 rounded-md bg-gray-900 px-3 py-1.5 text-xs font-medium text-white dark:bg-white dark:text-gray-900">
                Read More
                <x-heroicon-m-arrow-right class="h-3 w-3" />
            </span>
        </div>
    </div>
</div>

{{-- Footer Note --}}
<p class="mt-3 rounded-lg bg-gray-50 p-3 text-center text-xs text-gray-400 dark:bg-gray-800 dark:text-gray-500">
    {{ __('admin.preview_shows_how_the_card_appears_on_the_live_website') }}
</p>
