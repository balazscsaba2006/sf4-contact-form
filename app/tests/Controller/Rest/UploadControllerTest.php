<?php

namespace App\Tests\Controller\Rest;

use App\Tests\ApiTestCase;
use App\Tests\FunctionalTestCaseUtilsTrait;
use App\Tests\WebTestCaseUtilsTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class UploadControllerTest.
 */
class UploadControllerTest extends ApiTestCase
{
    use WebTestCaseUtilsTrait;
    use FunctionalTestCaseUtilsTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        static::bootKernel();

        $this->entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->createSchema($this->entityManager);
    }

    /**
     * Tests unallowed methods don't work on /api/upload URI.
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
     * Tests successfully submitting the upload form with all records valid.
     */
    public function testPostFormSuccessfullyWithAllRecordsValid(): void
    {
        $client = $this->prepareTest('correct.csv');

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJsonEquals(['status' => 'ok'], $client->getResponse()->getContent());
    }

    /**
     * Tests successfully submitting the upload form with all records being invalid.
     */
    public function testPostFormSuccessfullyWithAllRecordsBeingInvalid(): void
    {
        $client = $this->prepareTest('all_records_invalid.csv');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJsonStructure([
            'code',
            'message',
            'errors' => [
                'children' => [
                    'file' => [
                        'errors' => [],
                    ],
                ],
            ],
        ], $client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), false);
        $this->assertEquals([
            'Error(s) on row #1 field email: This value is not a valid email address.',
            'Error(s) on row #2 field email: This value is not a valid email address.',
            'Error(s) on row #3 field message: The message cannot be longer than 1000 characters.',
        ], $content->errors->children->file->errors);
    }

    /**
     * Tests successfully submitting the upload form with partial valid records.
     */
    public function testPostFormSuccessfullyWithPartialValidRecords(): void
    {
        $client = $this->prepareTest('partial_valid_records.csv');

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJsonStructure([
            'status',
            'message',
            'errors' => [
                'children' => [
                    'file' => [
                        'errors' => [],
                    ],
                ],
            ],
        ], $client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), false);
        $this->assertEquals('partially_ok', $content->status);
        $this->assertEquals([
            'Error(s) on row #1 field email: This value is not a valid email address.',
        ], $content->errors->children->file->errors);
    }

    /**
     * Tests POST request with no file on the /api/upload URI.
     */
    public function testPostWithNoFile(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/upload');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    /**
     * Tests submitting the upload form with an empty CSV.
     */
    public function testPostWithEmptyCsv(): void
    {
        $client = $this->prepareTest('empty_content.csv');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('Validation Failed', $content->message);
        $this->assertEquals(
            'File contains no records.',
            $content->errors->children->file->errors[0]
        );
    }

    /**
     * Tests submitting the upload form with too large CSV.
     */
    public function testPostWithTooLargeCsv(): void
    {
        $client = $this->prepareTest('too_large.csv');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('Validation Failed', $content->message);
        $this->assertEquals(
            'The file is too large (1066.34 kB). Allowed maximum size is 1024 kB.',
            $content->errors->children->file->errors[0]
        );
    }

    /**
     * Tests submitting the upload form with incorrect CSV.
     */
    public function testPostWithIncorrectCsv(): void
    {
        $client = $this->prepareTest('incorrect.csv');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertEquals('Validation Failed', $content->message);
        $this->assertEquals(
            'Each line must contain exactly 2 column(s), 1 column(s) found.',
            $content->errors->children->file->errors[0]
        );
    }

    /**
     * Tests submitting the upload form with wrong file type.
     */
    public function testPostWithWrongFileType(): void
    {
        $client = $this->prepareTest('test.pdf');
        $content = json_decode($client->getResponse()->getContent(), false);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
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
     * @param string $name name of the uploaded file
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

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropDatabase($this->entityManager);
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
