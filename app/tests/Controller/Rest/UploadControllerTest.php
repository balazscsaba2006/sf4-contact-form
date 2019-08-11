<?php

namespace App\Tests\Controller\Rest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class UploadControllerTest
 * @package App\Tests\Controller\Api
 */
class UploadControllerTest extends WebTestCase
{
    /**
     * Tests unallowed methods don't work on /api/upload URI
     *
     * @dataProvider getUnallowedMethods
     *
     * @param string $method
     */
    public function testUnallowedMethods(string $method)
    {
        $this->expectException(MethodNotAllowedHttpException::class);

        $client = static::createClient();
        $client->catchExceptions(false);
        $client->request($method, '/api/upload');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests POST request with empty file on the /api/upload URI
     */
    public function testPostEmptyFile()
    {
        $client = static::createClient();

        $client->request('POST', '/api/upload');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests POST request on the /api/upload URI
     */
    public function testPostUploadedFile()
    {
        $client = static::createClient();
        $file = new UploadedFile(
            __DIR__ .'/../../data/correct.csv',
            'correct.csv',
            'text/plain',
            null,
            true
        );
        $client->request(
            'POST',
            '/api/upload',
            [],
            ['upload' => ['file' => $file]]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function getUnallowedMethods(): array
    {
        return [
            ['GET'],
            ['HEAD'],
            ['PUT'],
            ['DELETE'],
            ['OPTIONS'],
        ];
    }
}
