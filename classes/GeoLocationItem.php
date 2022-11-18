<?php

namespace Ideaseven\GeoLocation\Classes;

class GeoLocationItem
{
    /** @var string */
    public $ipAddress;

    /** @var string ISO country code */
    public $countryCode;

    /** @var string */
    public $countryName;

    /** @var string Also known as state in US */
    public $regionName;

    /** @var string */
    public $cityName;

    /** @var string */
    public $zipCode;

    /** @var string */
    public $latitude;

    /** @var string */
    public $longitude;

    /** @var string - UTC+ in form "+02:00" */
    public $timeZone;

    /**
     * @param string $ip
     */
    public function __construct($ip = null)
    {
        $this->ipAddress = $ip;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "ipAddress: {$this->ipAddress} \n
                countryCode: {$this->countryCode} \n
                countryName: {$this->countryName} \n
                regionName: {$this->regionName} \n
                cityName: {$this->cityName} \n
                zipCode: {$this->zipCode} \n
                latitude: {$this->latitude} \n
                longitude: {$this->longitude} \n
                timeZone: {$this->timeZone} \n";
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ipAddress' => $this->ipAddress,
            'countryCode' => $this->countryCode,
            'countryName' => $this->countryName,
            'regionName' => $this->regionName,
            'cityName' => $this->cityName,
            'zipCode' => $this->zipCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timeZone' => $this->timeZone,
        ];
    }


}
