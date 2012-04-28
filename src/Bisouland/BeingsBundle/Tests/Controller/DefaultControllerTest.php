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
}
