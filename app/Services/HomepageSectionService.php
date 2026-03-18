<?php

namespace App\Services;

use App\Models\HomepageSection;

class HomepageSectionService
{
    public function updateSection(string $key, array $data): HomepageSection
    {
        /** @var HomepageSection $section */
        $section = HomepageSection::query()->firstOrNew(['section_key' => $key]);

        $section->fill($data)->save();

        return $section;
    }

    public function getSection(string $key): ?HomepageSection
    {
        return HomepageSection::query()->where('section_key', $key)->first();
    }
}
