<?php

namespace Bisouland\GameSystemBundle\Tests;

require_once dirname(__DIR__).'/../../../app/AppKernel.php';

class KernelAwareUnitTestCase extends \PHPUnit_Framework_TestCase
{
    protected $kernel;
    protected $container;

    public function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();

        parent::setUp();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();

        parent::tearDown();
    }
}
