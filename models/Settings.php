<?php

namespace Raccoon\GeoLocation\Models;

use Backend\Classes\FormField;
use Model;

/**
 * @method static get($settingName)
 */
class Settings extends Model
{
    const SERVICE_IP_INFO_DB = 'ipinfodb';
    const SERVICE_MAX_MIND_DB = 'maxmind';

    const FIELD_GEO_API_SERVICE = 'geo_api_service';
    const FIELD_MAX_MIND_DATABASE_PATH = 'max_mind_database_path';
    const FIELD_IP_INFO_API_KEY = 'ip_info_api_key';
    const FIELD_IP_INFO_ASYNCHRONOUS = 'ip_info_asynchronous';
    const FIELD_IP_MAX_REQUEST_TIME = 'ip_info_max_request_time';
    const FIELD_IP_LOCATION_TTL = 'ip_info_location_ttl';

    const IP_INFO_DB_FIELDS = [
        self::FIELD_IP_INFO_API_KEY,
        self::FIELD_IP_MAX_REQUEST_TIME,
        self::FIELD_IP_LOCATION_TTL,
        self::FIELD_IP_INFO_ASYNCHRONOUS,
    ];
    /**
     * @var array
     */
    public $implement = ['System.Behaviors.SettingsModel'];

    /**
     * @var string
     */
    public $settingsCode = 'raccoon_geolocation_settings';

    /**
     * @var string
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @param FormField[] $fields
     */
    public function filterFields($fields)
    {
        if ($fields->{self::FIELD_GEO_API_SERVICE}->value !== self::SERVICE_IP_INFO_DB) {
            foreach (self::IP_INFO_DB_FIELDS as $v) {
                $fields->{$v}->hidden = true;
                $fields->{$v}->disabled = true;
            }
        }
        if ($fields->{self::FIELD_GEO_API_SERVICE}->value !== self::SERVICE_MAX_MIND_DB) {
            $fields->{self::FIELD_MAX_MIND_DATABASE_PATH}->hidden = true;
            $fields->{self::FIELD_MAX_MIND_DATABASE_PATH}->disabled = true;
        }
    }
}
