<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\CancellationPolicy;
use App\Models\CancellationPolicyRule;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CountrySetupTask;
use App\Enums\SetupTask;

class CancellationPolicyManage extends Page
{
    protected static ?string $slug = 'manage-cancellation-policy';
    protected static ?string $title = 'Cancellation Policies';
    protected static ?string $navigationLabel = 'Cancellation Policy';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.cancellation-policy-manage';

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Location & Policies';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Cancellation Policies';
    }

    public function getSubheading(): ?string
    {
        return "Manage refund rules for all bookings.";
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    // --- State ---
    public ?string $cutoff_time = null;

    // Rules array for the UI display
    public $rulesList = [];

    // The current adding/editing state
    public $editingRuleId = null;
    public $days_before = null;
    public $is_refundable = 'Yes, Refundable';
    public $refund_percent = 100;
    
    public $showForm = false;

    protected function getActivePolicy(): CancellationPolicy
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Directly scope policies to the active country context from the top navigation dropdown
        $countryId = $user->current_country_id ?? DB::table('countries')->first()->id ?? 1;
        
        $propertyTypeId = DB::table('property_types')->first()->id ?? 1;

        return CancellationPolicy::firstOrCreate(
            [
                'country_id' => $countryId,
                'property_type_id' => $propertyTypeId,
            ],
            ['cancellation_cutoff_time' => '14:00:00', 'is_active' => true]
        );
    }

    public function mount()
    {
        $policy = $this->getActivePolicy();
        $this->cutoff_time = $policy->cancellation_cutoff_time ? substr($policy->cancellation_cutoff_time, 0, 5) : '14:00';
        
        $this->loadRules();

        // Force conscious interaction: Pre-fill and open a 0-day fallback rule form if no rules exist
        if (count($this->rulesList) === 0) {
            $this->addNewRule();
            $this->days_before = 0;
            $this->is_refundable = 'Non - Refundable';
            $this->refund_percent = 0;
        }
    }

    public function loadRules()
    {
        $policy = $this->getActivePolicy();
        $this->rulesList = $policy->rules()->orderByDesc('days_before_checkin')->get()->toArray();
    }

    public function saveCutoffTime()
    {
        $this->validate([
            'cutoff_time' => 'required',
        ]);
        
        $policy = $this->getActivePolicy();
        $policy->update(['cancellation_cutoff_time' => $this->cutoff_time]);

        $this->checkAndMarkComplete($policy);

        Notification::make()->title('Cutoff time saved successfully.')->success()->send();
    }

    public function addNewRule()
    {
        $this->resetRuleForm();
        $this->showForm = true;
    }

    public function editRule($ruleId)
    {
        $rule = CancellationPolicyRule::find($ruleId);
        if ($rule) {
            $this->editingRuleId = $rule->id;
            $this->days_before = $rule->days_before_checkin;
            $this->refund_percent = $rule->refund_percentage;
            $this->is_refundable = $rule->refund_percentage > 0 ? 'Yes, Refundable' : 'Non - Refundable';
            $this->showForm = true;
        }
    }

    public function deleteRule($ruleId)
    {
        $rule = CancellationPolicyRule::find($ruleId);
        // Prevent deleting the 0 days rule if we want to make it mandatory
        if ($rule && $rule->days_before_checkin === 0) {
            Notification::make()->title('Cannot delete the mandatory 0 day fallback rule.')->danger()->send();
            return;
        }
        
        if ($rule) {
            $rule->delete();
            $this->loadRules();
            Notification::make()->title('Rule deleted.')->success()->send();
        }
    }

    public function saveRule()
    {
        $this->validate([
            'days_before' => 'required|integer|min:0',
            'is_refundable' => 'required|string',
            'refund_percent' => 'required|integer|min:0|max:100',
        ]);

        if ($this->is_refundable === 'Non - Refundable') {
            $this->refund_percent = 0;
        }

        $policy = $this->getActivePolicy();

        // Check unique constraint
        $existing = CancellationPolicyRule::where('cancellation_policy_id', $policy->id)
            ->where('days_before_checkin', $this->days_before)
            ->where('id', '!=', $this->editingRuleId)
            ->first();

        if ($existing) {
            $this->addError('days_before', 'A rule for this many days already exists.');
            return;
        }

        if ($this->editingRuleId) {
            CancellationPolicyRule::where('id', $this->editingRuleId)->update([
                'days_before_checkin' => $this->days_before,
                'refund_percentage' => $this->refund_percent,
            ]);
        } else {
            CancellationPolicyRule::create([
                'cancellation_policy_id' => $policy->id,
                'days_before_checkin' => $this->days_before,
                'refund_percentage' => $this->refund_percent,
            ]);
        }

        $this->showForm = false;
        $this->resetRuleForm();
        $this->loadRules();

        $this->checkAndMarkComplete($policy);

        Notification::make()->title('Rule saved successfully.')->success()->send();
    }

    public function cancelRule()
    {
        $this->showForm = false;
        $this->resetRuleForm();
    }

    private function resetRuleForm()
    {
        $this->editingRuleId = null;
        $this->days_before = null;
        $this->is_refundable = 'Yes, Refundable';
        $this->refund_percent = 100;
        $this->resetErrorBag();
    }

    protected function checkAndMarkComplete(CancellationPolicy $policy)
    {
        if (empty($policy->cancellation_cutoff_time)) {
            return;
        }

        $hasFallbackRule = false;
        foreach ($this->rulesList as $rule) {
            if ($rule['days_before_checkin'] === 0) {
                $hasFallbackRule = true;
                break;
            }
        }

        if (! $hasFallbackRule) {
            $hasFallbackRule = $policy->rules()->where('days_before_checkin', 0)->exists();
        }

        if ($hasFallbackRule) {
            CountrySetupTask::markComplete(SetupTask::CancellationPolicy, $policy->country_id);
        }
    }
}
