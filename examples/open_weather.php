<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

require '../vendor/autoload.php';

use Defr\OpenWeather;

$weather = new OpenWeather();
$forecast = $weather->getForecast();

echo '<h1>'.$forecast->getCity().'</h1>';

echo sprintf(
    '<p>Teplota: %s °C<br>Počasí: %s<br>Východ slunce: %s<br>Západ slunce: %s<br>%s<p>',
    $forecast->getTemperature(),
    $forecast->getDescription(),
    $forecast->getSunrise()->format('H:i:s'),
    $forecast->getSunset()->format('H:i:s'),
    $forecast->getImageHtmlTag()
);
