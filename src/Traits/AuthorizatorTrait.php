<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator\Traits;

use SklinetNette\Authorizator\Authorizator;

/**
 * Trait AuthorizatorTrait
 *
 * @package SklinetNette\Authorizator\Traits
 */
trait AuthorizatorTrait
{
    /**
     * @var \SklinetNette\Authorizator\Authorizator
     */
    private $authorizator;

    public function __construct(Authorizator $authorizator)
    {
        $this->authorizator = $authorizator;
    }

    /**
     * Can a user access resource?
     *
     * @param  string  $attribute
     * @param  mixed $subject
     *
     * @return bool
     */
    public function isGranted($attribute, $subject): bool
    {
        return $this->authorizator->isGranted($attribute, $subject);
    }
}