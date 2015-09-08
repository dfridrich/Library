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

    /**
     * @param \DateTime $sunset
     * @param \DateTime $sunrise
     * @param $city
     * @param $description
     * @param $icon
     * @param $temperature
     */
    public function __construct(\DateTime $sunset,
                                \DateTime $sunrise,
                                $city,
                                $description,
                                $icon,
                                $temperature)
    {
        $this->sunset = $sunset;
        $this->sunrise = $sunrise;
        $this->city = $city;
        $this->description = $description;
        $this->icon = $icon;
        $this->temperature = $temperature;
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
}
