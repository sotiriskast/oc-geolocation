<?php namespace skwebsolution\GeoLocation;

use skwebsolution\GeoLocation\Components\VisitorLocation;
use skwebsolution\GeoLocation\Models\Settings;
use skwebsolution\GeoLocation\Providers\GeoLocationServiceProvider;
use System\Classes\PluginBase;
/**
 * GeoLocation Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'GeoLocation',
            'description' => 'Retrieve geo location information from IP address',
            'author' => 'Gatis Pelcers',
            'icon' => 'icon-compass'
        ];
    }

    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            VisitorLocation::class => 'geoLocation',
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        app()->register(GeoLocationServiceProvider::class);
        return [];
    }

    public function registerPermissions()
    {
        return [];
    }


    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'skwebsolution GeoLocation',
                'description' => 'Configure geo location services, API keys and settings.',
                'icon' => 'icon-compass',
                'class' => Settings::class,
                'order' => 500,
                'keywords' => 'geolocation ip location country state geo skwebsolution'
            ]
        ];
    }


}
