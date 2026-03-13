<?php

namespace App\Services;

use App\Enums\SetupTask;
use App\Models\City;
use App\Models\CountrySetupTask;

class CityService
{
    public function createCity(array $data, int $countryId): City
    {
        $data['country_id'] = $countryId;

        $city = City::query()->create($data);

        $isFirstCity = City::query()
            ->forCountry($countryId)
            ->count() === 1;

        if ($isFirstCity) {
            CountrySetupTask::markComplete(SetupTask::Cities, $countryId);
        }

        return $city;
    }

    public function updateCity(City $city, array $data): City
    {
        $city->update($data);

        return $city;
    }
}
