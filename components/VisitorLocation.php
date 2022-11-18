<?php namespace Ideaseven\GeoLocation\Components;

use Cms\Classes\ComponentBase;
use Ideaseven\GeoLocation\Classes\GeoLocationItem;
use Ideaseven\GeoLocation\Facades\Geo;

class VisitorLocation extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'VisitorLocation Component',
            'description' => 'Provides geo location info based on visitors IP address. Used as plugin capabilities demo.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    /**
     * @return GeoLocationItem
     */
    public function data()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Geo::getLocation(request()->ip());
    }
}
