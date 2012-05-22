<?php

namespace Bisouland\BeingsBundle\Entity\Factory;

use Bisouland\BeingsBundle\Entity\Kiss;
use Bisouland\BeingsBundle\Entity\Being;

class KissFactory
{
    private $kisser;
    private $kissed;
    private $damages;
    private $reward;

    public function __construct(Being $kisser, Being $kissed, $report)
    {
        $this->kisser = $kisser;
        $this->kissed = $kissed;
        $this->damages = $report['defenderDamages'];
        $this->reward = $report['attackerReward'];
    }
    
    public function make()
    {
        $kiss = new Kiss();
        $kiss->setDamages($this->damages);
        $kiss->setReward($this->reward);
        $kiss->setKisser($this->kisser);
        $kiss->setKissed($this->kissed);
        
        return $kiss;
    }
}
