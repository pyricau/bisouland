<?php

namespace Bisouland\BeingsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase; 
use Bisouland\BeingsBundle\Controller\UpdateController;

class UpdateControllerTest extends WebTestCase
{
    public function testBirthGeneration()
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
    
    public function testNumberMaximumNumberOfBirthInONeDay()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        for ($numberOfBirth = 0; $numberOfBirth < UpdateController::$maximumNumberOfBirthInOneDay; $numberOfBirth++) {
            $client->request('GET', '/beings/');
        }
        
        $client->request('GET', '/beings/');

        $this->assertTrue(UpdateController::$maximumNumberOfBirthInOneDay == $em->getRepository('BisoulandBeingsBundle:Being')->countBirthsToday());
    }
}
