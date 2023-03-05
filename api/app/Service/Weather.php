<?php

namespace App\Service;

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Weather {

    /**
     * @param $lat
     * @param $lng
     *
     * @return false|object
     */
    public function getWeatherByLatLng($lat, $lng)
    {
        try {
            return Http::get($this->getFormattedUrl($lat, $lng));
        } catch (HttpClientException $ex) {
            // return false;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getWeatherApiKey()
    {
        return Config::get('app.weather_api_key');
    }

    /**
     * @return mixed
     */
    public function getWeatherApiUrl()
    {
        return Config::get('app.weather_api_url');
    }

    /**
     * @param $lat
     * @param $lng
     *
     * @return string
     */
    public function getFormattedUrl($lat, $lng)
    {
        $queryParams = [
            'lat' => $lat,
            'lon' => $lng,
            'appId' => $this->getWeatherApiKey(),
        ];

        return $this->getWeatherApiUrl() . '?' . http_build_query($queryParams);
    }
}
