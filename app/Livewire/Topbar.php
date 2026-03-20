<?php

namespace App\Livewire;

use App\Models\Country;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Livewire\Concerns\HasUserMenu;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Topbar extends Component implements HasActions, HasSchemas
{
    use HasUserMenu;
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[On('refresh-topbar')]
    public function refresh(): void {}

    public function switchCountry(int $countryId): void
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->switchCountry($countryId);

        $this->redirect(request()->header('Referer', '/'));
    }

    public function getOperatingCountriesProperty(): Collection
    {
        return Country::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getCurrentCountryProperty(): ?Country
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if (! $user?->current_country_id) {
            return null;
        }

        return Country::query()->find($user->current_country_id);
    }

    public function getLanguagesProperty(): Collection
    {
        return \App\Models\Language::query()
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    public function getCurrentLanguageProperty()
    {
        $locale = session('locale', \Illuminate\Support\Facades\App::getLocale());
        return \App\Models\Language::where('code', $locale)->first() 
            ?? \App\Models\Language::where('is_default', true)->first();
    }

    public function switchLanguage(string $code): void
    {
        session(['locale' => $code]);
        \Illuminate\Support\Facades\App::setLocale($code);
        $this->redirect(request()->header('Referer', '/'));
    }

    public function render(): View
    {
        return view('livewire.topbar');
    }
}
