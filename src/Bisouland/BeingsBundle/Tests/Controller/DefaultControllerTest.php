<?php

namespace Bisouland\BeingsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Bisouland\BeingsBundle\DataFixtures\ORM\LoadBeingData; 
use Bisouland\BeingsBundle\Controller\DefaultController;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/beings/');

        $this->assertTrue($crawler->filter('title:contains("Bisouland v2 - Personnages")')->count() > 0);
    }
    
    public function testPresenceOfNamesInView()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/beings/');

        $beings = LoadBeingData::getFixtures();
        foreach ($beings as $being) {
            $this->assertTrue(0 < $crawler->filter('td:contains("'.$being['name'].'")')->count());
        }
    }
}
