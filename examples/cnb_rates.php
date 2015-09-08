<?php

require "../vendor/autoload.php";

use Defr\CnbRates;
use Defr\CnbRates\Rate;
use Defr\CnbRates\Rates;

$cnbRates = new CnbRates();
/** @var Rates $rates */
$rates = $cnbRates->getRates();

echo "<h1>" . $rates->getDate()->format('d.m.Y') . "</h1>";

var_dump($rates->getEur()->getCode());
var_dump($rates->getEur());

/** @var Rate $rate */
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