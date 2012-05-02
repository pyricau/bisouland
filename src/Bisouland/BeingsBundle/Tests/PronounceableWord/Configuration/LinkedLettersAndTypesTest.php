<?php

namespace Bisouland\BeingsBundle\Tests\PronounceableWord\Configuration;

use Bisouland\BeingsBundle\PronounceableWord\Configuration\LetterTypes;
use Bisouland\BeingsBundle\PronounceableWord\Configuration\LinkedLetters;

require_once __DIR__.'/../../../../../../vendor/gnugat/PronounceableWord/test/PronounceableWord/Tests/Configuration/LinkedLettersAndTypesTest.php';

class LinkedLettersAndTypesTest extends \PronounceableWord_Tests_Configuration_LinkedLettersAndTypesTest {
    public function setUp() {
        $this->letterTypesConfiguration = new LetterTypes();
        $this->linkedLettersConfiguration = new LinkedLetters();
    }
}
