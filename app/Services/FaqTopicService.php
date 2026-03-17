<?php

namespace App\Services;

use App\Models\FaqTopic;
use Illuminate\Support\Str;

class FaqTopicService
{
    public function createTopic(array $data): FaqTopic
    {
        $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $data['title']);
        $data['sort_order'] = FaqTopic::query()->max('sort_order') + 1;

        return FaqTopic::query()->create($data);
    }

    public function updateTopic(FaqTopic $topic, array $data): FaqTopic
    {
        unset($data['slug']);

        $topic->update($data);

        return $topic;
    }

    public function deleteTopic(FaqTopic $topic): void
    {
        $topic->delete();

        $this->reSequence();
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 2;

        while (FaqTopic::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function reSequence(): void
    {
        FaqTopic::query()
            ->orderBy('sort_order')
            ->get()
            ->each(function (FaqTopic $topic, int $index): void {
                $topic->update(['sort_order' => $index + 1]);
            });
    }
}
