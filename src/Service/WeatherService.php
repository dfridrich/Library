<?php

namespace Defr\ToolBagBundle\Service;

use Defr\OpenWeather;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class GridService
 * @package Defr\ToolBagBundle\Service
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class WeatherService
{

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var \Defr\OpenWeather
     */
    protected $weather;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->weather = new OpenWeather($this->kernel->getCacheDir());
    }

    public function getWeather($city = null)
    {
        return $this->weather->getForecast($city);
    }

}