<?php

namespace SklinetNette\Authorizator;

use SklinetNette\Authorizator\Security\Voter\IVoter;
use Nette\DI\Container;

/**
 * Class VotersLoader
 *
 * @package SklinetNette\Authorizator
 */
class VotersLoader
{
    /**
     * @var \Nette\DI\Container
     */
    private $container;

    /**
     * @var \SklinetNette\Authorizator\Security\Voter\IVoter[]|null
     */
    private $cache = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get voters object from DI container
     *
     * @return \SklinetNette\Authorizator\Security\Voter\IVoter[]
     */
    public function getVoters(): array
    {
        //check if there are no voters in cache
        if($this->cache) {
            return $this->cache;
        }

        /** @var \SklinetNette\Authorizator\Security\Voter\IVoter[] $voters */
        $voters = [];

        $servicesNames = $this->container->findByType(IVoter::class);

        //load voters classes/objects from DI container
        foreach ($servicesNames as $key => $servicesName) {
            $voters[$servicesName] = $this->container->getService($servicesName);
        }

        //cache voters objects
        $this->cache = $voters;

        return $this->cache;
    }
}
