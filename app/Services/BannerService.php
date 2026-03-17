<?php

namespace App\Services;

use App\Models\Banner;

class BannerService
{
    public function createBanner(array $data, int $countryId): Banner
    {
        $data['country_id'] = $countryId;

        return Banner::query()->create($data);
    }

    public function updateBanner(Banner $banner, array $data): Banner
    {
        $banner->update($data);

        return $banner;
    }

    public function deleteBanner(Banner $banner): void
    {
        $banner->delete();
    }
}
