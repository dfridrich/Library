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
use PHPUnit\Framework\TestCase;

class OpenWeatherTest extends TestCase
{
    public function testWeatherInPrague()
    {
        $weather = new OpenWeather(null, $_ENV['WEATHER_API']);
        $forecast = $weather->getForecast('Prague, Czech republic');

        $this->assertIsFloat($forecast->getTemperature());
        $this->assertInstanceOf('\DateTime', $forecast->getSunset());
    }
}
