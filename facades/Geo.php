<?php

namespace Raccoon\GeoLocation\Facades;

use October\Rain\Support\Facade;
use Raccoon\GeoLocation\Classes\GeoLocationService;

class Geo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return app(GeoLocationService::class);
    }
}