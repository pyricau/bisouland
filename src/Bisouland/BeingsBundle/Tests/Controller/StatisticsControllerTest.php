<?php

namespace Bisouland\BeingsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Bisouland\BeingsBundle\DataFixtures\ORM\LoadBeingData; 
use Bisouland\BeingsBundle\Controller\StatisticsController;

class StatisticsControllerTest extends WebTestCase
{  
    public function testBeingGeneration()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        
        $routes = array(
            '/',
            '/beings/',
        );
        foreach ($routes as $route) {
            $numberBefore = $em->getRepository('BisoulandBeingsBundle:Being')->countAlivePopulation();

            $client->request('GET', $route);
            
            $numberAfter = $em->getRepository('BisoulandBeingsBundle:Being')->countAlivePopulation();

            $this->assertTrue(1 === $numberAfter - $numberBefore);
        }
    }
    
    public function testNumberMaxOfBirthPerDay()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        for ($numberOfBirth = 0; $numberOfBirth < StatisticsController::$numberMaxOfBirthPerDay; $numberOfBirth++) {
            $client->request('GET', '/beings/');
        }
        
        $client->request('GET', '/beings/');

        $this->assertTrue(StatisticsController::$numberMaxOfBirthPerDay == $em->getRepository('BisoulandBeingsBundle:Being')->countBirthsToday());
    }
}
