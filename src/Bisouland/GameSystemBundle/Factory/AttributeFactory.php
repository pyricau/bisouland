<?php

namespace Bisouland\GameSystemBundle\Factory;

use Bisouland\GameSystemBundle\Factory\RollFactory;

class AttributeFactory
{
    static public $numberOfDice = 4;
    static public $diceNumberOfSides = 6;
    static public $numberOfBestDiceResultToKeep = 3;

    private $rollResults = array();

    private $rollFactory;

    public function __construct(RollFactory $rollFactory)
    {
        $this->rollFactory = $rollFactory;
        $this->rollFactory->setNumberOfSides(self::$diceNumberOfSides);
    }

    public function make()
    {
        $this->rollAttributeDice();
        $this->keepBestResults();

        return $this->addResults();
    }

    private function rollAttributeDice()
    {
        for ($numberOfRoll = 0; $numberOfRoll < self::$numberOfDice; $numberOfRoll++) {
            $rollResult = $this->rollFactory->make();

            array_push($this->rollResults, $rollResult);
        }
    }

    private function keepBestResults()
    {
        rsort($this->rollResults);
        $this->rollResults = array_slice(
                $this->rollResults,
                0,
                self::$numberOfBestDiceResultToKeep
        );
    }

    private function addResults()
    {
        return array_sum($this->rollResults);
    }
}
