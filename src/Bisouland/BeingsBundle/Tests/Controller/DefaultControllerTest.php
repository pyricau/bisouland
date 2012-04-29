<?php

namespace Bisouland\BeingsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/beings/');

        $this->assertTrue($crawler->filter('title:contains("Bisouland v2 - Personnages")')->count() > 0);
    }
    
    public function testNames()
    {
        $names = array(
            'Smith',
            'John',
            'Adam',
            'Douglas',
            'Terry',
        );

        $client = static::createClient();

        $crawler = $client->request('GET', '/beings/');

        foreach ($names as $name) {
            $this->assertTrue($crawler->filter('td:contains("'.$name.'")')->count() > 0);
        }
    }
}
