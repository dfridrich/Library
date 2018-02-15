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

/**
 * Class Rate.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class Rate
{
    /**
     * @var \DateTime
     */
    public $date;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var float
     */
    public $rate;

    /**
     * @param \DateTime $date
     * @param $code
     * @param $country
     * @param $currency
     * @param $amount
     * @param $rate
     */
    public function __construct(\DateTime $date, $code, $country, $currency, $amount, $rate)
    {
        $this->date = $date;
        $this->code = $code;
        $this->country = $country;
        $this->currency = $currency;
        $this->amount = (float) $amount;
        $this->rate = (float) $rate;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return (string) ($this->rate);
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }
}
