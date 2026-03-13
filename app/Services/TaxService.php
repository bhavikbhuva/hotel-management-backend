<?php

namespace App\Services;

use App\Enums\SetupTask;
use App\Models\CountrySetupTask;
use App\Models\PropertyType;
use App\Models\Tax;

class TaxService
{
    public function createTax(array $data, int $countryId): Tax
    {
        $data['country_id'] = $countryId;
        $data['property_type_id'] = PropertyType::query()
            ->where('is_active', true)
            ->value('id');

        $tax = Tax::query()->create($data);

        $isFirstTax = Tax::query()
            ->forCountry($countryId)
            ->count() === 1;

        if ($isFirstTax) {
            CountrySetupTask::markComplete(SetupTask::Taxes, $countryId);
        }

        return $tax;
    }

    public function updateTax(Tax $tax, array $data): Tax
    {
        $tax->update($data);

        return $tax;
    }

    public function deleteTax(Tax $tax): void
    {
        $tax->delete();
    }
}
