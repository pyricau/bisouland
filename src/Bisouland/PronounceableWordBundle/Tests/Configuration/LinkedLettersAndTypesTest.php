<?php

namespace Bisouland\PronounceableWordBundle\Tests\Configuration;

use Bisouland\PronounceableWordBundle\Configuration\LetterTypes;
use Bisouland\PronounceableWordBundle\Configuration\LinkedLetters;

require_once __DIR__.'/../../../../../../vendor/gnugat/PronounceableWord/test/PronounceableWord/Tests/Configuration/LinkedLettersAndTypesTest.php';

class LinkedLettersAndTypesTest extends \PronounceableWord_Tests_Configuration_LinkedLettersAndTypesTest {
    public function setUp() {
        $this->letterTypesConfiguration = new LetterTypes();
        $this->linkedLettersConfiguration = new LinkedLetters();
    }
}
