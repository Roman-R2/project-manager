<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('http://localhost/login', $this->client->getResponse()->headers->get('Location'));
    }

    public function testSuccess(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'mail@mail.com',
            'PHP_AUTH_PW' => '111111'
        ]);
        $crawler = $client->request('GET', '/');
        $this->assertSame(200 , $client->getResponse()->getStatusCode());
        $this->assertContains('Hello', $crawler->filter('H1')->text());
    }
}
