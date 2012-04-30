<?php

namespace Bisouland\BeingsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Bisouland\BeingsBundle\DataFixtures\ORM\LoadBeingData; 

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
        $client = static::createClient();

        $crawler = $client->request('GET', '/beings/');

        $beings = LoadBeingData::getFixtures();
        foreach ($beings as $being) {
            $this->assertTrue(0 < $crawler->filter('td:contains("'.$being['name'].'")')->count());
        }
    }
    
    public function testBirth()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        
        $routes = array(
            '/beings/',
        );
        foreach ($routes as $route) {
            $beings = $em->getRepository('BisoulandBeingsBundle:Being')
                    ->findAll();
            $numberBefore = count($beings);

            $client->request('GET', $route);
            
            $beings = $em->getRepository('BisoulandBeingsBundle:Being')
                    ->findAll();
            $numberAfter = count($beings);

            $this->assertTrue(1 === $numberAfter - $numberBefore);
        }
    }
}
