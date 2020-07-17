<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator;

use SklinetNette\Authorizator\Security\Voter\IVoter;
use SklinetNette\Authorizator\Security\Voter\Voter;
use Nette\DI\Container;

/**
 * Class Authorizator
 *
 * @package SklinetNette\Authorizator
 */
class Authorizator
{
    /**
     * @var \Nette\Security\User
     */
    private $securityUser;

    /**
     * @var \SklinetNette\Authorizator\VotersLoader
     */
    private $votersLoader;

    public function __construct(\Nette\Security\User $securityUser, VotersLoader $votersLoader)
    {
        $this->securityUser = $securityUser;
        $this->votersLoader = $votersLoader;
    }

    /**
     * Can a user access resource?
     *
     * @param  string  $attribute
     * @param  mixed $subject
     *
     * @return bool
     */
    public function isGranted(string $attribute, $subject): bool
    {
        return $this->callVoter($attribute, $subject);
    }

    /***************************************************************************************************************
     * Helpers
     ***************************************************************************************************************/


    /**
     * Call a right voter class if exists. If does not exists return always false
     *
     * @param  string|  $attributes
     * @param $subject
     *
     * @return bool
     */
    private function callVoter(string $attribute, $subject): bool
    {
        $voters = $this->votersLoader->getVoters();

        /** @var int $vote Default vote result */
        $vote = Voter::ACCESS_ABSTAIN;

        //iterate over all existing voter clasess
        foreach ($voters as $key => $voter) {
            /** @var string[] $attributes */
            $attributes = (array) $attribute;

            //result
            $vote = $voter->vote($this->securityUser, $subject, $attributes);

            //allow
            if($vote > 0) {
                return true;
            }
        }

        //deny
        if ($vote <= 0) {
            return false;
        }
    }


}
