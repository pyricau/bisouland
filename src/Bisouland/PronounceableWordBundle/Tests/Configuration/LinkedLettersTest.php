<?php

namespace Bisouland\PronounceableWordBundle\Tests\Configuration;

use Bisouland\PronounceableWordBundle\Configuration\LinkedLetters;

require_once __DIR__.'/../../../../../vendor/gnugat/PronounceableWord/test/PronounceableWord/Tests/Configuration/LinkedLettersTest.php';

class LinkedLettersTest extends \PronounceableWord_Tests_Configuration_LinkedLettersTest {
    public function setUp() {
        $this->configuration = new LinkedLetters();
    }
}
