<?php

namespace App\Tests\Controller\Rest;

use App\Tests\Controller\WebTestCaseControllerTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class UploadControllerTest
 * @package App\Tests\Controller\Api
 */
class UploadControllerTest extends WebTestCase
{
    use WebTestCaseControllerTrait;

    /**
     * Tests unallowed methods don't work on /api/upload URI
     *
     * @dataProvider getUnallowedMethods
     *
     * @param string $method
     */
    public function testUnallowedMethods(string $method): void
    {
        $this->expectException(MethodNotAllowedHttpException::class);

        $client = static::createClient();
        $client->catchExceptions(false);
        $client->request($method, '/api/upload');

        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests successfully submitting the upload form
     */
    public function testSubmitUploadFormSuccessfully(): void
    {
        $client = $this->prepareTest('correct.csv');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('ok', $content->status);
    }

    /**
     * Tests POST request with empty file on the /api/upload URI
     */
    public function testPostEmptyFile(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/upload');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests submitting the upload form with too large CSV
     */
    public function testSubmitUploadFormWithTooLargeCsv(): void
    {
        $client = $this->prepareTest('too_large.csv');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('Validation Failed', $content->message);
        $this->assertEquals(
            'The file is too large (1066.34 kB). Allowed maximum size is 1024 kB.',
            $content->errors->children->file->errors[0]
        );
    }

    /**
     * Tests submitting the upload form with incorrect CSV
     */
    public function testSubmitUploadFormWithIncorrectCsv(): void
    {
        $this->markTestSkipped('must be revisited.');

        $client = $this->prepareTest('incorrect.csv');
        $content = json_decode($client->getResponse()->getContent(), false);

//        $this->assertEquals(400, $client->getResponse()->getStatusCode());
//        $this->assertEquals('Validation Failed', $content->message);
//        $this->assertEquals('The file is too large (1066.34 kB). Allowed maximum size is 1024 kB.', $content->errors->children->file->errors[0]);
    }

    /**
     * Tests submitting the upload form with wrong file type
     */
    public function testSubmitUploadFormWithWrongFileType(): void
    {
        $client = $this->prepareTest('test.pdf');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('Validation Failed', $content->message);
        $this->assertEquals(
            'The mime type of the file is invalid ("application/pdf"). Allowed mime types are: "text/plain", "text/csv", "application/csv", "text/x-csv", "application/x-csv", "text/x-comma-separated-values", "text/comma-separated-values"',
            $content->errors->children->file->errors[0]
        );
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

    /**
     * @param string $name Name of the uploaded file.
     *
     * @return KernelBrowser
     */
    private function prepareTest(string $name): KernelBrowser
    {
        $client = static::createClient();
        $file = $this->getUploadedFile($name);

        $client->request(
            'POST',
            '/api/upload',
            [],
            ['upload' => ['file' => $file]]
        );

        return $client;
    }
}
