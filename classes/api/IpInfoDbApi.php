<?php

namespace Ideaseven\GeoLocation\Classes\Api;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use PDOException;
use Queue;
use Ideaseven\GeoLocation\Classes\GeoLocationApiInterface;
use Ideaseven\GeoLocation\Classes\GeoLocationException;
use Ideaseven\GeoLocation\Classes\GeoLocationItem;
use Ideaseven\GeoLocation\Classes\GetLocationFromInfoDbJob;
use Ideaseven\GeoLocation\Models\IpInfoLocation;
use Ideaseven\GeoLocation\Models\Settings;

class IpInfoDbApi implements GeoLocationApiInterface
{
    /** @var int API request timeout for both connection and request in seconds */
    private $timeout;

    /** @var string */
    private $endpoint;

    /** @var int Hours */
    private $ttl;

    /** @var bool */
    private $shouldCallAsynchronous;

    /**
     * @throws GeoLocationException
     */
    public function __construct()
    {
        $apiKey = Settings::get(Settings::FIELD_IP_INFO_API_KEY);

        if (!$apiKey) {
            throw new GeoLocationException('IP Info DB API key is not set');
        }
        $this->timeout = (int)Settings::get(Settings::FIELD_IP_MAX_REQUEST_TIME);
        $this->ttl = (int)Settings::get(Settings::FIELD_IP_LOCATION_TTL);
        $this->shouldCallAsynchronous = (bool)Settings::get(Settings::FIELD_IP_INFO_ASYNCHRONOUS);
        $this->endpoint = "http://api.ipinfodb.com/v3/ip-city/?key={$apiKey}&ip=%s&format=json";
    }

    /**
     * @param string $ip
     * @return GeoLocationItem
     * @throws Exception
     */
    public function getLocation($ip)
    {
        // First we look for previously stored geo location information
        $storedLocation = $this->getFromDatabase($ip);

        if ($storedLocation) {
            return $storedLocation;
        }

        // Actual geo location API call will be made asynchronously. Now just create an empty database entry and queue a job
        if ($this->shouldCallAsynchronous) {
            $location = new GeoLocationItem($ip);
            $this->storeLocation($location);
            Queue::push(GetLocationFromInfoDbJob::class, [
                'ip' => $ip,
            ]);

            return $location;
        }

        // Retrieve geo location info from API and store it in database
        try {
            $location = $this->getLocationFromApi($ip);
        } catch (Exception $e) {
            throw new GeoLocationException($e->getMessage());
        }

        return $location;
    }

    /**
     * @param string $ip
     * @return GeoLocationItem
     */
    public function getLocationFromApi($ip)
    {
        $endpoint = sprintf($this->endpoint, $ip);
        $response = $this->executeRequest($endpoint);

        // Request was successful but location could not be determined.
        // This can happen randomly for some IP addresses
        if (!$response) {
            return new GeoLocationItem($ip);
        }
        // Store retrieved geo location data in DB
        $location = $this->fromApiResponse($response);
        $this->storeLocation($location);

        return $location;
    }

    /**
     * @param string $endpoint
     * @return array|bool
     */
    private function executeRequest($endpoint)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $rawResponse = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) {
            return false;
        }

        $response = json_decode($rawResponse, true);
        if ($response['statusCode'] !== 'OK') {
            return false;
        }

        return $response;
    }

    /**
     * If location exists, it gets updated by IP address. Otherwise new row is inserted
     *
     * @param GeoLocationItem $location
     * @return bool
     */
    private function storeLocation(GeoLocationItem $location)
    {
        try {
            $row = DB::table(IpInfoLocation::TABLE)
                ->where('ip', $location->ipAddress)
                ->first();

            // Dunno why, but updateOrInsert call just throws an undefined method error.
            // Probably something October specific
            if ($row) {
                return DB::table(IpInfoLocation::TABLE)
                    ->where('ip', $location->ipAddress)
                    ->update([
                        'ip' => $location->ipAddress,
                        'country_code' => $location->countryCode,
                        'country' => $location->countryName,
                        'state' => $location->regionName,
                        'city' => $location->cityName,
                        'zip' => $location->zipCode,
                        'latitude' => (float)$location->latitude,
                        'longitude' => (float)$location->longitude,
                        'timezone' => $location->timeZone,
                        'updated_at' => Carbon::now(),
                    ]);
            }

            return DB::table(IpInfoLocation::TABLE)
                ->insert([
                    'ip' => $location->ipAddress,
                    'country_code' => $location->countryCode,
                    'country' => $location->countryName,
                    'state' => $location->regionName,
                    'city' => $location->cityName,
                    'zip' => $location->zipCode,
                    'latitude' => (float)$location->latitude,
                    'longitude' => (float)$location->longitude,
                    'timezone' => $location->timeZone,
                    'updated_at' => Carbon::now(),
                ]);

        } catch (PDOException $e) {
            // Concurrency problem, two requests at the same time try to insert the same value
            // Can just be ignored
            return false;
        }
    }

    /**
     * @param string $ip
     * @return GeoLocationItem|null
     */
    private function getFromDatabase($ip)
    {
        $updatedAt = Carbon::now()->subHours($this->ttl);
        $row = DB::table(IpInfoLocation::TABLE)
            ->where('ip', $ip)
            ->where('updated_at', '>=', $updatedAt)
            ->first();

        if (!$row) {
            return null;
        }

        return $this->fromDatabaseRow((array)$row);
    }

    /**
     * @param array $row
     * @return GeoLocationItem
     */
    private function fromDatabaseRow(array $row = [])
    {
        $re = new GeoLocationItem($row['ip']);

        $re->ipAddress = $row['ip'] ?? null;
        $re->countryCode = $row['country_code'] ?? null;
        $re->countryName = $row['country'] ?? null;
        $re->regionName = $row['state'] ?? null;
        $re->cityName = $row['city'] ?? null;
        $re->zipCode = $row['zip'] ?? null;
        $re->latitude = $row['latitude'] ?? null;
        $re->longitude = $row['longitude'] ?? null;
        $re->timeZone = $row['timezone'] ?? null;

        return $re;
    }

    /**
     * @param array $response
     * @return GeoLocationItem
     */
    private function fromApiResponse(array $response = [])
    {
        $re = new GeoLocationItem($response['ipAddress']);

        $re->ipAddress = $this->getFilteredValue($response['ipAddress']) ?? null;
        $re->countryCode = $this->getFilteredValue($response['countryCode']) ?? null;
        $re->countryName = $this->getFilteredValue($response['countryName']) ?? null;
        $re->regionName = $this->getFilteredValue($response['regionName']) ?? null;
        $re->cityName = $this->getFilteredValue($response['cityName']) ?? null;
        $re->zipCode = $this->getFilteredValue($response['zipCode']) ?? null;
        $re->latitude = $this->getFilteredValue($response['latitude']) ?? null;
        $re->longitude = $this->getFilteredValue($response['longitude']) ?? null;
        $re->timeZone = $this->getFilteredValue($response['timeZone']) ?? null;

        return $re;
    }

    /**
     * For some IP addreses that could not be resolved a dash as value is randomly returned from API
     *
     * @param mixed $value
     * @return mixed
     */
    private function getFilteredValue($value)
    {
        if ($value === '-') {
            return null;
        }

        return $value;
    }
}
