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

use Defr\CnbRates;

$cnbRates = new CnbRates();
$rates = $cnbRates->getRates();

echo '<h1>'.$rates->getDate()->format('d.m.Y').'</h1>';

var_dump($rates->getEur()->getCode());
var_dump($rates->getEur());

foreach ($rates as $rate) {
    echo sprintf(
        '%s (%s), %s %s = %s Kč<br>',
        $rate->getCountry(),
        $rate->getCode(),
        $rate->getAmount(),
        $rate->getCurrency(),
        $rate->getRate()
    );
}
