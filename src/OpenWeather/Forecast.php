<?php

namespace Defr\OpenWeather;

/**
 * Class Forecast
 * @package Defr\OpenWeather
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class Forecast
{
    const IMAGE_PNG = "png";
    const IMAGE_JPG = "jpg";

    private $sunset;
    private $sunrise;
    private $city;
    private $description;
    private $icon;
    private $temperature;
    private $pressure;
    private $humidity;
    private $tempMin;
    private $tempMax;
    private $windSpeed;
    private $windDeg;
    private $image;

    /**
     * @param \DateTime $sunset
     * @param \DateTime $sunrise
     * @param $city
     * @param $description
     * @param $icon
     * @param $temperature
     * @param $pressure
     * @param $humidity
     * @param $tempMin
     * @param $tempMax
     * @param $windSpeed
     * @param $windDeg
     * @param $image
     */
    public function __construct(\DateTime $sunset,
                                \DateTime $sunrise,
                                $city,
                                $description,
                                $icon,
                                $temperature,
                                $pressure,
                                $humidity,
                                $tempMin,
                                $tempMax,
                                $windSpeed,
                                $windDeg,
                                $image)
    {
        $this->sunset = $sunset;
        $this->sunrise = $sunrise;
        $this->city = $city;
        $this->description = $description;
        $this->icon = $icon;
        $this->temperature = $temperature;
        $this->pressure = $pressure;
        $this->humidity = $humidity;
        $this->tempMin = $tempMin;
        $this->tempMax = $tempMax;
        $this->windSpeed = $windSpeed;
        $this->windDeg = $windDeg;
        $this->image = $image;
    }

    /**
     * @return \DateTime
     */
    public function getSunset()
    {
        return $this->sunset;
    }

    /**
     * @return \DateTime
     */
    public function getSunrise()
    {
        return $this->sunrise;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @return mixed
     */
    public function getPressure()
    {
        return $this->pressure;
    }

    /**
     * @return mixed
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * @return mixed
     */
    public function getTempMin()
    {
        return $this->tempMin;
    }

    /**
     * @return mixed
     */
    public function getTempMax()
    {
        return $this->tempMax;
    }

    /**
     * @return mixed
     */
    public function getWindSpeed()
    {
        return $this->windSpeed;
    }

    /**
     * @return mixed
     */
    public function getWindDeg()
    {
        return $this->windDeg;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getImage($type = self::IMAGE_JPG)
    {
        return $this->image . '.' . $type;
    }

    /**
     * @param string $type
     * @param bool|false $getHtmlTag
     * @return string
     */
    public function getImageBlob($type = self::IMAGE_JPG, $getHtmlTag = false)
    {
        $location = sprintf(__DIR__ . "/../../assets/OpenWeather/%s/%s.%s", $type, $this->image, $type);
        $blob = sprintf('data:image/%s;base64,%s',
            $type,
            base64_encode(file_get_contents($location))
        );

        if ($getHtmlTag) {
            return sprintf(
                '<img src="%s" alt="%s" title="%s" class="open-weather-image">',
                $blob,
                $this->description,
                $this->description
            );
        } else {
            return $blob;
        }
    }

    /**
     * @param string $type
     * @return string
     */
    public function getImageHtmlTag($type = self::IMAGE_JPG)
    {
        return $this->getImageBlob($type, true);
    }
}
