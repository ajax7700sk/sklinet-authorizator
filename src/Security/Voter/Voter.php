<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator\Security\Voter;

use SklinetNette\Authorizator\Tracy\VoterPanel;
use Nette\Security\User;

/**
 * Class Voter
 *
 * @package SklinetNette\Authorizator\Security\Voter
 */
abstract class Voter implements IVoter
{
    /** @var \Nette\Security\User */
    protected $securityUser;

    /**
     * {@inheritdoc}
     */
    public function vote(\Nette\Security\User $securityUser, $subject, array $attributes): int
    {
        $this->securityUser = $securityUser;

        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            if ($this->voteOnAttribute($attribute, $subject, $securityUser)) {
                $vote = self::ACCESS_GRANTED;

                //log granted vote
                VoterPanel::addVoteResult($attribute, $subject, get_called_class(), $vote);

                // grant access as soon as at least one attribute returns a positive response
                return $vote;
            }

            //log denied vote
            VoterPanel::addVoteResult($attribute, $subject, get_called_class(), $vote);
        }

        //log abstain vote
        VoterPanel::addVoteResult($attribute, $subject, get_called_class(), $vote);

        return $vote;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    abstract protected function supports(string $attribute, $subject): bool;

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed  $subject
     * @param \Nette\Security\User $securityUser
     *
     * @return bool
     */
    abstract protected function voteOnAttribute(string $attribute, $subject, \Nette\Security\User $securityUser): bool;

}
