<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UploadControllerTest
 * @package App\Tests\Controller
 */
class UploadControllerTest extends WebTestCase
{
    /**
     * Tests GET request on the /upload URI
     */
    public function testGetUploadPage()
    {
        $client = static::createClient();

        $client->request('GET', '/upload');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
