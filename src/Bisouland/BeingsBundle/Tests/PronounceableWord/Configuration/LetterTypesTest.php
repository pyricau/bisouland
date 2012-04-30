<?php

namespace Bisouland\BeingsBundle\Tests\PronounceableWord\Configuration;

use Bisouland\BeingsBundle\PronounceableWord\Configuration\LetterTypes;

require_once __DIR__.'/../../../../../../vendor/gnugat/PronounceableWord/test/PronounceableWord/Tests/Configuration/LetterTypesTest.php';

class LetterTypesTest extends \PronounceableWord_Tests_Configuration_LetterTypesTest {
    public function setUp() {
        $this->configuration = new LetterTypes();
    }
}
