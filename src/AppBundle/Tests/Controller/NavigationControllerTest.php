<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NavigationControllerTest extends WebTestCase
{
    public function testListnavigablelinks()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/listNavigableLinks');
    }

}
