<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator\Security\Voter;

/**
 * Interface IVoter
 *
 * @package SklinetNette\Authorizator\Security\Voter
 */
interface IVoter
{
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param \Nette\Security\User $securityUser
     * @param mixed $subject    The subject to secure
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(\Nette\Security\User $securityUser, $subject, array $attributes): int;
}
