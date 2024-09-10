<?php

namespace skwebsolution\GeoLocation\Facades;

use October\Rain\Support\Facade;
use skwebsolution\GeoLocation\Classes\GeoLocationService;

class Geo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GeoLocationService::class;
    }
}