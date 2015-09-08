<?php

namespace Defr\ToolBagBundle\Service;

use Defr\CnbRates;
use Defr\OpenWeather;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CnbRatesService
 * @package Defr\ToolBagBundle\Service
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class CnbRatesService
{

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var \Defr\CnbRates
     */
    protected $cnbRates;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->cnbRates = new CnbRates($this->kernel->getCacheDir());
    }

    /**
     * @param \DateTime $date
     * @return CnbRates\Rates
     */
    public function getRates(\DateTime $date = null)
    {
        if ($date === null) $date = new \DateTime();
        return $this->cnbRates->getRates($date);
    }

}