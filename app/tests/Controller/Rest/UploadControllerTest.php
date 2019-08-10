<?php

namespace App\Tests\Controller\Rest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UploadControllerTest
 * @package App\Tests\Controller\Api
 */
class UploadControllerTest extends WebTestCase
{
    /**
     * Tests POST request on the /api/upload URI
     */
    public function testPostUploadFile()
    {
        $client = static::createClient();

        $client->request('POST', '/api/upload');

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
