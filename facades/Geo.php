<?php

namespace Ideaseven\GeoLocation\Facades;

use October\Rain\Support\Facade;
use Ideaseven\GeoLocation\Classes\GeoLocationService;

class Geo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return app(GeoLocationService::class);
    }
}
