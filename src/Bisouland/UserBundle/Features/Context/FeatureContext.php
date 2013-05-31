<?php

namespace Bisouland\UserBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Behat\Behat\Exception\PendingException;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

use Behat\Symfony2Extension\Context\KernelAwareInterface;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Feature context.
 */
class FeatureContext extends MinkContext implements KernelAwareInterface
{
    private $kernel;
    private $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^(?:|I )am logged in as "(?P<user>(?:[^"]|\\")*)"$/
     *
     * @param string $user
     *
     * @return array
     */
    public function loggedInAsUser($user)
    {
        return array(
            new Step\When('I am on "/login"'),
            new Step\When('I fill in "security.login.username" with "'.$user.'"'),
            new Step\When('I fill in "security.login.password" with "password"'),
            new Step\When('I press "security.login.submit"'),
            new Step\When('I should see "layout.logout"'),
        );
    }
}
