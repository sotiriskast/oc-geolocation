<?php namespace Ideaseven\Location;

use Ideaseven\GeoLocation\Components\VisitorLocation;
use Ideaseven\GeoLocation\Facades\Geo;

use Ideaseven\GeoLocation\Models\Settings;
use Ideaseven\GeoLocation\Providers\GeoLocationServiceProvider;
use Backend;
use System\Classes\PluginBase;

/**
 * Location Plugin Information File
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
            'name'        => 'Location',
            'description' => 'No description provided yet...',
            'author'      => 'Ideaseven',
            'icon'        => 'icon-compass'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        app()->register(GeoLocationServiceProvider::class);
        return [];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
//        return []; // Remove this line to activate

        return [
            VisitorLocation::class => 'geoLocation',
//            'Ideaseven\Location\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'ideaseven.location.some_permission' => [
                'tab' => 'Location',
                'label' => 'Some permission'
            ],
        ];
    }
    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'Ideaseven GeoLocation',
                'description' => 'Configure geo location services, API keys and settings.',
                'icon' => 'icon-compass',
                'class' => Settings::class,
                'order' => 500,
                'keywords' => 'geolocation ip location country state geo Ideaseven'
            ]
        ];
    }
    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'location' => [
                'label'       => 'Location',
                'url'         => Backend::url('ideaseven/location/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['ideaseven.location.*'],
                'order'       => 500,
            ],
        ];
    }
    public function registerMarkupTags()
    {
        return [
            'getLocationInformation' => function () {
                return \Ideaseven\GeoLocation\Facades\Geo::getLocation($_SERVER['REMOTE_ADDR']);
            },
        ];
    }
}
