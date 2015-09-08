<?php

require "../vendor/autoload.php";

use Defr\NameDays;

$today = NameDays::getNameDay();
$tomorrow = NameDays::getNameDay(new \DateTime('tomorrow'));

echo sprintf('Dnes má svátek %s, zítra %s', $today, $tomorrow);
