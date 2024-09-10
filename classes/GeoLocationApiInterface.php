<?php

namespace skwebsolution\GeoLocation\Classes;

interface GeoLocationApiInterface
{
    /**
     * @param string $ip
     * @return GeoLocationItem
     */
    public function getLocation($ip);
}