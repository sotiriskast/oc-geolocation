<?php

namespace Ideaseven\GeoLocation\Classes\Api;

use DateTime;
use DateTimeZone;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\GeoIp2Exception;
use Ideaseven\GeoLocation\Classes\GeoLocationApiInterface;
use Ideaseven\GeoLocation\Classes\GeoLocationItem;
use Ideaseven\GeoLocation\Models\Settings;

class MaxMindGeoApi implements GeoLocationApiInterface
{
    /** @var Reader */
    private $reader;

    public function __construct()
    {
        // Free GeoLite2 City database included with plugin
        $databasePath = __DIR__ . '/../../database/GeoLite2-City.mmdb';
        // Custom Geo IP database provided by user
        if (Settings::get(Settings::FIELD_MAX_MIND_DATABASE_PATH)) {
            $databasePath = storage_path(Settings::get(Settings::FIELD_MAX_MIND_DATABASE_PATH));
        }
        $this->reader = new Reader($databasePath);
    }

    /**
     * @param string $ip
     * @return GeoLocationItem
     */
    public function getLocation($ip)
    {
        $re = new GeoLocationItem($ip);
        try {
            $record = $this->reader->city($ip);
        } catch (GeoIp2Exception $e) {
            // Record probably not found in database, return empty item
            return $re;
        }

        $re->cityName = $record->city->name;
        $re->countryCode = $record->country->isoCode;
        $re->countryName = $record->country->name;
        $re->regionName = $record->mostSpecificSubdivision->name;
        $re->zipCode = $record->postal->code;
        $re->latitude = number_format($record->location->latitude, 5, '.', '');
        $re->longitude = number_format($record->location->longitude, 5, '.', '');
        $re->timeZone = $this->getTimezone($record->location->timeZone);

        return $re;
    }

    private function getTimezone($timeZone)
    {
        if ($timeZone) {
            $date = new DateTime('now', new DateTimeZone($timeZone));
            return $date->format('P');
        } else {
            return null;
        }
    }
}
