<?php

/*
 * This file is part of the bisouland project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bisouland\Test\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllerTestCase extends WebTestCase
{
    /** @var Symfony\Bundle\FrameworkBundle\Client */
    protected $client;

    /** @var array */
    protected $parameters = array();

    /**
     * @param string $name
     * @param string $value
     */
    protected function givenThisParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @param string $name
     * @param string $value
     */
    protected function andThisParameter($name, $value)
    {
        $this->givenThisParameter($name, $value);
    }

    /**
     * @param string $method
     * @param string $route
     */
    protected function whenRequesting($method, $route)
    {
        $this->client = static::createClient();

        $this->client->request($method, $route, $this->parameters);
    }

    protected function thenItShouldSucceed()
    {
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }
}
