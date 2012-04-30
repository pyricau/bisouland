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
    
    public function testBeingGeneration()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        
        $routes = array(
            '/beings/',
        );
        foreach ($routes as $route) {
            $numberBefore = $em->getRepository('BisoulandBeingsBundle:Being')->count();

            $client->request('GET', $route);
            
            $numberAfter = $em->getRepository('BisoulandBeingsBundle:Being')->count();

            $this->assertTrue(1 === $numberAfter - $numberBefore);
        }
    }
    
    public function testNumberMaxOfBirthPerDay()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        for ($numberOfBirth = 0; $numberOfBirth < DefaultController::$numberMaxOfBirthPerDay; $numberOfBirth++) {
            $client->request('GET', '/beings/');
        }
        
        $client->request('GET', '/beings/');

        $this->assertTrue(DefaultController::$numberMaxOfBirthPerDay == $em->getRepository('BisoulandBeingsBundle:Being')->countBirthsToday());
    }
}
