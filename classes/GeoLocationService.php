<?php

namespace skwebsolution\GeoLocation\Classes;

class GeoLocationService
{
    /** @var GeoLocationItem[] Array keys are IP addresses */
    private $cached = [];

    /** @var GeoLocationApiInterface */
    private $api;

    public function __construct(GeoLocationApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Looks for location item in cache array. If not exists - tries to retrieve location data from API
     *
     * @param string $ip
     * @return GeoLocationItem
     */
    public function getLocation($ip)
    {
        if ($this->isCached($ip)) {
            return $this->cached[$ip];
        }

        $this->cached[$ip] = $this->api->getLocation($ip);

        return $this->cached[$ip];
    }

    /**
     * @param string $ip
     * @return bool
     */
    private function isCached($ip)
    {
        return isset($this->cached[$ip]);
    }
}