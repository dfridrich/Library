<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

namespace Defr;

use Defr\OpenWeather\Forecast;

/**
 * Class OpenWeather.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class OpenWeather
{
    const API_URL = 'http://api.openweathermap.org/data/2.5/weather?q=%s&lang=%s&units=%s&appid=%s';
    const DEFAULT_CITY = 'Prague, CZ';

    const UNITS_METRIC = 'metric';
    const UNITS_IMPERIAL = 'imperial';

    private $cacheDir;

    private $apiKey;

    /**
     * @param null $cacheDir
     * @param null $apiKey
     */
    public function __construct($cacheDir = null, $apiKey = null)
    {
        if (null === $cacheDir) {
            $cacheDir = sys_get_temp_dir();
        }
        $this->cacheDir = $cacheDir.'/defr';
        $this->apiKey = $apiKey;
    }

    /**
     * @param null   $city
     * @param string $lang
     * @param string $units
     *
     * @return Forecast
     */
    public function getForecast($city = null, $lang = 'cz', $units = self::UNITS_METRIC)
    {
        $city = null === $city ? self::DEFAULT_CITY : $city;
        $cachedFileName = sprintf("%s_%s.json", date('YmdH'), hash('crc32', $city.$lang.$units));

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $cachedFile = $this->cacheDir.'/weather_'.$cachedFileName;

        if (!is_file($cachedFile)) {
            $url = sprintf(self::API_URL, urlencode($city), $lang, $units, $this->apiKey);
            $json = json_decode(file_get_contents($url));
            if (!empty($json)) {
                file_put_contents($cachedFile, json_encode($json, JSON_PRETTY_PRINT));
            }
        } else {
            $json = json_decode(file_get_contents($cachedFile));
        }

        $forecast = new Forecast(
            (new \DateTime())->setTimestamp($json->sys->sunset),
            (new \DateTime())->setTimestamp($json->sys->sunrise),
            $json->name,
            $json->weather[0]->description,
            $this->getFontAwesomeIcons()[mb_strtolower($json->weather[0]->main)],
            $json->main->temp,
            $json->main->pressure,
            $json->main->humidity,
            $json->main->temp_min,
            $json->main->temp_max,
            isset($json->wind->speed) ? $json->wind->speed : null,
            isset($json->wind->deg) ? $json->wind->deg : null,
            $this->getWeatherImages()[mb_strtolower($json->weather[0]->main)]
        );

        return $forecast;
    }

    /**
     * @return array
     */
    private function getFontAwesomeIcons()
    {
        return [
            'clear' => 'fa fa-sun-o',
            'clouds' => 'fa fa-cloud',
            'mist' => 'fa fa-cloud',
            'rain' => 'fa fa-tint',
            'snow' => 'fa fa-empire',
            'storm' => 'fa fa-flash',
            'thunderstorm' => 'fa fa-flash',
        ];
    }

    /**
     * @return array
     */
    private function getWeatherImages()
    {
        return [
            'clear' => 'clear',
            'clouds' => 'clouds',
            'mist' => 'mist',
            'rain' => 'rain',
            'snow' => 'snow',
            'storm' => 'storm',
            'thunderstorm' => 'thunderstorm',
        ];
    }
}
