<?php

namespace skwebsolution\GeoLocation\Classes;

use Illuminate\Queue\Jobs\Job;
use skwebsolution\GeoLocation\Classes\Api\IpInfoDbApi;

class GetLocationFromInfoDbJob
{
    /**
     * @param Job $job
     * @param array $data Contains IP that needs to be queried against IPInfoDb API
     */
    public function fire(Job $job, $data)
    {
        $api = new IpInfoDbApi();
        $api->getLocationFromApi($data['ip']);

        $job->delete();
    }
}