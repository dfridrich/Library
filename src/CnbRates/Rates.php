<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

namespace Defr\CnbRates;

use ArrayIterator;
use DateTime;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 * @implements IteratorAggregate<Rates>
 */
class Rates implements IteratorAggregate
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var array
     */
    private $rates;

    /**
     * @param array     $rates
     */
    public function __construct(DateTime $date, array $rates)
    {
        $this->date = $date;
        $this->rates = $rates;
    }

    /**
     * @param $currency
     *
     * @throws InvalidArgumentException
     * @return Rate
     */
    public function getRate($currency = 'EUR')
    {
        $currency = mb_strtoupper($currency);
        if (array_key_exists($currency, $this->rates)) {
            return $this->rates[$currency];
        }
        throw new InvalidArgumentException('Kurz pro měnu '.$currency.' neexistuje.');
    }

    /**
     * @return Rate
     */
    public function getUsd()
    {
        return $this->getRate('USD');
    }

    /**
     * @return Rate
     */
    public function getEur()
    {
        return $this->getRate('EUR');
    }

    /**
     * @return Rate
     */
    public function getGbp()
    {
        return $this->getRate('GBP');
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->rates);
    }
}
