<?php

namespace Raccoon\GeoLocation\Providers;

use October\Rain\Support\ServiceProvider;
use Raccoon\GeoLocation\Classes\Api\IpInfoDbApi;
use Raccoon\GeoLocation\Classes\Api\MaxMindGeoApi;
use Raccoon\GeoLocation\Classes\GeoLocationService;
use Raccoon\GeoLocation\Models\Settings;

class GeoLocationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GeoLocationService::class, function () {
            // Based on Settings injects correct API class
            $apiMap = [
                Settings::SERVICE_IP_INFO_DB => IpInfoDbApi::class,
                Settings::SERVICE_MAX_MIND_DB => MaxMindGeoApi::class,
            ];
            $apiClass = $apiMap[Settings::get(Settings::FIELD_GEO_API_SERVICE)];
            return new GeoLocationService(new $apiClass());
        });
    }
}