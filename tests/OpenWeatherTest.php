<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

use Defr\OpenWeather;

class OpenWeatherTest extends PHPUnit_Framework_TestCase
{
    public function testWeatherInPrague()
    {
        $weather = new OpenWeather(null, $_ENV['WEATHER_API']);
        $forecast = $weather->getForecast('Prague, Czech republic');

        $this->assertInternalType('float', $forecast->getTemperature());
        $this->assertInstanceOf('\DateTime', $forecast->getSunset());
    }
}
