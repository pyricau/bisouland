<?php

namespace Bisouland\GameSystemBundle\Factory;

class AttributeFactory
{
    static public $minimumDiceValue = 1;

    static public $numberOfDice = 4;
    static public $diceNumberOfSides = 6;
    static public $numberOfBestDiceResultToKeep = 3;

    private $rollResults = array();

    public function make()
    {
        $this->rollAttributeDice();
        $this->keepBestResults();

        return $this->addResults();
    }

    private function rollAttributeDice()
    {
        for ($numberOfRoll = 0; $numberOfRoll < self::$numberOfDice; $numberOfRoll++) {
            $rollResult = mt_rand(self::$minimumDiceValue, self::$diceNumberOfSides);
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
