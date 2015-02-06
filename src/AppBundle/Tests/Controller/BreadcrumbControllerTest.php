<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BreadcrumbControllerTest extends WebTestCase
{
    public function testRender()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/render');
    }

}
