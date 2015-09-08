<?php

namespace Defr;

use Defr\OpenWeather\Forecast;

/**
 * Class OpenWeather.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class OpenWeather
{
    const API_URL = 'http://api.openweathermap.org/data/2.5/weather?q=%s&lang=%s&units=%s';
    const DEFAULT_CITY = 'Prague, CZ';

    const UNITS_METRIC = 'metric';
    const UNITS_IMPERIAL = 'imperial';

    private $cacheDir;

    /**
     * @param null $cacheDir
     */
    public function __construct($cacheDir = null)
    {
        if ($cacheDir === null) {
            $cacheDir = sys_get_temp_dir();
        }
        $this->cacheDir = $cacheDir.'/defr';
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
        $city = $city == null ? self::DEFAULT_CITY : $city;
        $cachedFileName = md5(date('YmdH').$city.$lang.$units).'.json';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $cachedFile = $this->cacheDir.'/weather_'.$cachedFileName;

        if (!is_file($cachedFile)) {
            $url = sprintf(self::API_URL, urlencode($city), $lang, $units);
            $json = json_decode(file_get_contents($url));
            file_put_contents($cachedFile, json_encode($json, JSON_PRETTY_PRINT));
        } else {
            $json = json_decode(file_get_contents($cachedFile));
        }

        $forecast = new Forecast(
            (new \DateTime())->setTimestamp($json->sys->sunset),
            (new \DateTime())->setTimestamp($json->sys->sunrise),
            $json->name,
            $json->weather{0}->description,
            $this->getFontAwesomeIcons()[strtolower($json->weather{0}->main)],
            $json->main->temp);

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
}
