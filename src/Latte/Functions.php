<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator\Latte;

use SklinetNette\Authorizator\Authorizator;
use Latte\Engine;

/**
 * Class Functions
 *
 * @package SklinetNette\Authorizator\Latte
 */
class Functions
{
    /** @var \Latte\Engine */
    private $latteEngine;
    /**
     * @var \SklinetNette\Authorizator\Authorizator
     */
    private $authorizator;

    public function __construct(Authorizator $authorizator)
    {
        $this->authorizator = $authorizator;
    }

    /**
     * Can user access resource?
     *
     * @param  string  $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    public function isGranted(string $attribute, $subject): bool
    {
        return call_user_func(
            [$this->authorizator, 'isGranted'],
            ...func_get_args()
        );
    }
}
