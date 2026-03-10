<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.setup')]
class SetupWizard extends Component
{
    /**
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
        Setting::set('setup_completed', 'true');

        Filament::setCurrentPanel(Filament::getDefaultPanel());
        Filament::auth()->login($user);

        $this->redirect('/');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.setup-wizard');
    }
}
