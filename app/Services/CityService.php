<?php

namespace App\Services;

use App\Enums\SetupTask;
use App\Models\City;
use App\Models\CountrySetupTask;
use App\Models\RefCity;
use App\Models\RefState;
use App\Models\State;

class CityService
{
    public function createCity(array $data, int $countryId): City
    {
        $refState = RefState::findOrFail($data['ref_state_id']);
        $state = $this->findOrCreateState($refState, $countryId);
        $cityData = $this->resolveCityData($data['ref_city_id'], $refState);

        $city = City::query()->create([
            'ref_city_id' => $cityData['ref_city_id'],
            'country_id' => $countryId,
            'state_id' => $state->id,
            'name' => $cityData['name'],
            'latitude' => $cityData['latitude'],
            'longitude' => $cityData['longitude'],
            'status' => $data['status'],
        ]);

        $isFirstCity = City::query()
            ->forCountry($countryId)
            ->count() === 1;

        if ($isFirstCity) {
            CountrySetupTask::markComplete(SetupTask::Cities, $countryId);
        }

        return $city;
    }

    public function updateCity(City $city, array $data, int $countryId): City
    {
        $refState = RefState::findOrFail($data['ref_state_id']);
        $state = $this->findOrCreateState($refState, $countryId);
        $cityData = $this->resolveCityData($data['ref_city_id'], $refState);

        $city->update([
            'ref_city_id' => $cityData['ref_city_id'],
            'state_id' => $state->id,
            'name' => $cityData['name'],
            'latitude' => $cityData['latitude'],
            'longitude' => $cityData['longitude'],
            'status' => $data['status'],
        ]);

        return $city;
    }

    /**
     * Resolve city data from either a ref_city or a state-as-city fallback.
     *
     * @return array{ref_city_id: int|null, name: string, latitude: string|null, longitude: string|null}
     */
    private function resolveCityData(string $refCityIdRaw, RefState $refState): array
    {
        // State-as-city fallback: "state:123" means use the state as the city
        if (str_starts_with($refCityIdRaw, 'state:')) {
            return [
                'ref_city_id' => null,
                'name' => $refState->name,
                'latitude' => $refState->latitude,
                'longitude' => $refState->longitude,
            ];
        }

        $refCity = RefCity::findOrFail((int) $refCityIdRaw);

        return [
            'ref_city_id' => $refCity->id,
            'name' => $refCity->name,
            'latitude' => $refCity->latitude,
            'longitude' => $refCity->longitude,
        ];
    }

    private function findOrCreateState(RefState $refState, int $countryId): State
    {
        return State::query()->firstOrCreate(
            ['ref_state_id' => $refState->id],
            [
                'country_id' => $countryId,
                'name' => $refState->name,
                'latitude' => $refState->latitude,
                'longitude' => $refState->longitude,
            ],
        );
    }
}
