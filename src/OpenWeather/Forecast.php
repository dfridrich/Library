<?php

namespace Defr\OpenWeather;

/**
 * Class Forecast.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class Forecast
{
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
                                $windDeg)
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
}
