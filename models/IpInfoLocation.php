<?php namespace Raccoon\GeoLocation\Models;

use Model;

/**
 * IpInfoLocation Model
 * Table stores IP location data retrieved from IP Info Database API
 */
class IpInfoLocation extends Model
{
    const TABLE = 'raccoon_geolocation_ip_info_locations';
    /**
     * @var string The database table used by the model.
     */
    public $table = self::TABLE;
}
