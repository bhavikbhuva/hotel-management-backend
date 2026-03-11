<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Country;
use App\Models\OperatingCountry;
use App\Models\PropertyType;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.setup')]
class SetupWizard extends Component
{
    use WithFileUploads;

    /**
     * component classs = logic + memory
     * view = presentation
     *
     * Current wizard step.
     * Step 1 = Admin Account, Step 2+ = configuration steps.
     */
    public int $currentStep = 1;

    /**
     * Total steps. Increment this as new config steps are added.
     * Currently: 1 (admin account) + 1 (system mode) = 2.
     */
    public int $totalSteps = 4;

    // ── Step 1: Admin Account ────────────────────────────────────────────────

    public string $name = '';

    public string $email = '';

    public ?string $phone = null;

    public string $password = '';

    public string $passwordConfirmation = '';

    // ── Step 2: System Mode ──────────────────────────────────────────────────

    public string $systemMode = 'single';

    // ── Step 3: Countries ─────────────────────────────────────────────────────

    public array $selectedCountries = [];

    public string $countrySearch = '';

    // ── Step 4: Property Type ─────────────────────────────────────────────────

    public string $selectedPropertyType = '';

    public bool $showAddPropertyTypeModal = false;

    public string $newPropertyTypeName = '';

    public string $newPropertyTypeDescription = '';

    public $newPropertyTypeIcon = null;

    // ────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        if (Setting::get('setup_completed') === 'true') {
            $this->redirect('/');
        }
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep >= $this->totalSteps) {
            $this->complete();

            return;
        }

        $this->currentStep++;
    }

    public function previousStep(): void
    {
        $this->currentStep = max(1, $this->currentStep - 1);
    }

    public function toggleCountry(int $countryId): void
    {
        if (in_array($countryId, $this->selectedCountries)) {
            $this->selectedCountries = array_values(
                array_diff($this->selectedCountries, [$countryId])
            );
        } else {
            $this->selectedCountries[] = $countryId;
        }
    }

    public function openAddPropertyTypeModal(): void
    {
        $this->showAddPropertyTypeModal = true;
    }

    public function closeAddPropertyTypeModal(): void
    {
        $this->showAddPropertyTypeModal = false;
        $this->newPropertyTypeName = '';
        $this->newPropertyTypeDescription = '';
        $this->newPropertyTypeIcon = null;
        $this->resetValidation(['newPropertyTypeName', 'newPropertyTypeDescription', 'newPropertyTypeIcon']);
    }

    public function createPropertyType(): void
    {
        $this->validate([
            'newPropertyTypeName' => ['required', 'string', 'max:255', 'unique:property_types,name'],
            'newPropertyTypeDescription' => ['required', 'string', 'max:1000'],
            'newPropertyTypeIcon' => ['required', 'file', 'max:5120', 'mimes:png,svg'],
        ]);

        $filename = $this->newPropertyTypeIcon->store('propertyTypes', 'public');

        PropertyType::query()->create([
            'name' => $this->newPropertyTypeName,
            'description' => $this->newPropertyTypeDescription,
            'icon' => basename($filename),
            'is_default' => false,
            'is_active' => false,
        ]);

        $this->closeAddPropertyTypeModal();
    }

    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['nullable', 'string', 'max:20'],
                'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
                'passwordConfirmation' => ['required', 'string'],
            ]),
            2 => $this->validate([
                'systemMode' => ['required', 'in:single,multi'],
            ]),
            3 => $this->validate([
                'selectedCountries' => ['required', 'array', 'min:1'],
                'selectedCountries.*' => ['integer', 'exists:countries,id'],
            ]),
            4 => $this->validate([
                'selectedPropertyType' => ['required', 'exists:property_types,id'],
            ]),
            default => null,
        };
    }

    protected function complete(): void
    {
        /** @var User $user */
        $user = User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'password' => $this->password,
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
        ]);

        Setting::set('system_mode', $this->systemMode);

        OperatingCountry::query()->delete();
        OperatingCountry::query()->insert(
            array_map(fn (int $id) => [
                'country_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ], $this->selectedCountries)
        );

        PropertyType::query()->update(['is_active' => false]);
        PropertyType::query()->where('id', $this->selectedPropertyType)->update(['is_active' => true]);

        Setting::set('setup_completed', 'true');

        Filament::setCurrentPanel(Filament::getDefaultPanel());
        Filament::auth()->login($user);

        $this->redirect('/');
    }

    public function render(): \Illuminate\View\View
    {
        $countries = $this->currentStep === 3
            ? Country::query()
                ->where('is_active', true)
                ->when($this->countrySearch, fn ($q) => $q
                    ->where('name', 'like', "%{$this->countrySearch}%")
                    ->orWhere('currency_name', 'like', "%{$this->countrySearch}%"))
                ->orderBy('name')
                ->get()
            : new Collection;

        $propertyTypes = $this->currentStep === 4
            ? PropertyType::query()->orderByDesc('is_default')->orderBy('name')->get()
            : new Collection;

        return view('livewire.setup-wizard', compact('countries', 'propertyTypes'));
    }
}
