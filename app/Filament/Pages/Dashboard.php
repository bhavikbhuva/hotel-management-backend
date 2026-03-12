<?php

namespace App\Filament\Pages;

use App\Enums\SetupTask;
use App\Models\CountrySetupTask;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    public function getTitle(): string|Htmlable
    {
        return parent::getTitle();
    }

    public function getHeading(): string|Htmlable
    {
        if (! $this->isCountrySetupComplete()) {
            return '';
        }

        return parent::getTitle();
    }

    protected function getViewData(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $countryId = $user->current_country_id;

        if (! $countryId) {
            return [
                'isSetupComplete' => false,
                'checklistTasks' => [],
                'completedCount' => 0,
                'totalTasks' => count(SetupTask::cases()),
            ];
        }

        $tasks = CountrySetupTask::query()
            ->forCountry($countryId)
            ->get()
            ->keyBy(fn (CountrySetupTask $task) => $task->task_key->value);

        $checklistTasks = collect(SetupTask::cases())->map(function (SetupTask $task) use ($tasks) {
            $record = $tasks->get($task->value);

            return [
                'key' => $task->value,
                'label' => $task->label(),
                'description' => $task->description(),
                'buttonLabel' => $task->buttonLabel(),
                'route' => $task->route(),
                'isCompleted' => $record?->completed_at !== null,
                'isGlobal' => $task->isGlobal(),
            ];
        });

        $completedCount = $checklistTasks->where('isCompleted', true)->count();

        return [
            'isSetupComplete' => $completedCount >= count(SetupTask::cases()),
            'checklistTasks' => $checklistTasks,
            'completedCount' => $completedCount,
            'totalTasks' => count(SetupTask::cases()),
        ];
    }

    public function getView(): string
    {
        if (! $this->isCountrySetupComplete()) {
            return 'filament.pages.dashboard-checklist';
        }

        return 'filament-panels::pages.page';
    }

    protected function isCountrySetupComplete(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! $user?->current_country_id) {
            return false;
        }

        return CountrySetupTask::isCountryFullySetup($user->current_country_id);
    }
}
