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

use Defr\NameDays;

$today = NameDays::getNameDay();
$tomorrow = NameDays::getNameDay(new \DateTime('tomorrow'));

echo sprintf('Dnes má svátek %s, zítra %s', $today, $tomorrow);
