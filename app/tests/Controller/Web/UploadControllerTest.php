<?php

namespace App\Tests\Controller\Web;

use App\Tests\FunctionalTestCaseUtilsTrait;
use App\Tests\WebTestCaseUtilsTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UploadControllerTest.
 */
class UploadControllerTest extends WebTestCase
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
     * Tests GET request on the /upload URI.
     */
    public function testGetUploadPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/upload');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests successfully submitting the upload form with all records valid.
     */
    public function testSubmitUploadFormSuccessfullyWithAllRecordsValid(): void
    {
        [$client, $crawler] = $this->prepareTest('correct.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
        self::assertSelectorTextContains('div.alert.alert-success', 'Nice job! The uploaded file was successfully saved.');
    }

    /**
     * Tests successfully submitting the upload form with all records being invalid.
     */
    public function testSubmitUploadFormSuccessfullyWithAllRecordsBeingInvalid(): void
    {
        [$client, $crawler] = $this->prepareTest('all_records_invalid.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-danger')->count());
        self::assertSelectorTextContains('div.alert.alert-danger', 'Whoops. All records are invalid, nothing could be saved.');
    }

    /**
     * Tests successfully submitting the upload form with partial valid records.
     */
    public function testSubmitUploadFormSuccessfullyWithPartialValidRecords(): void
    {
        [$client, $crawler] = $this->prepareTest('partial_valid_records.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-warning')->count());
        self::assertSelectorTextContains('div.alert.alert-warning', 'Successfully saved 2 of 3 records. Check rows: #1');
    }

    /**
     * Tests submitting the upload form with an empty CSV.
     */
    public function testSubmitUploadFormWithEmptyCsv(): void
    {
        [$client, $crawler] = $this->prepareTest('empty_content.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.form-error-message')->count());
        self::assertSelectorTextContains(
            '.form-error-message',
            'File contains no records.'
        );
    }

    /**
     * Tests submitting the upload form with too large CSV.
     */
    public function testSubmitUploadFormWithTooLargeCsv(): void
    {
        [$client, $crawler] = $this->prepareTest('too_large.csv');

        $a = $crawler->html();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.form-error-message')->count());

        self::assertSelectorTextContains(
            '.form-error-message',
            'The file is too large (1066.34 kB). Allowed maximum size is 1024 kB.'
        );
    }

    /**
     * Tests submitting the upload form with incorrect CSV.
     */
    public function testSubmitUploadFormWithIncorrectCsv(): void
    {
        [$client, $crawler] = $this->prepareTest('incorrect.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.form-error-message')->count());
        self::assertSelectorTextContains(
            '.form-error-message',
            'Each line must contain exactly 2 column(s), 1 column(s) found.'
        );
    }

    /**
     * Tests submitting the upload form with wrong file type.
     */
    public function testSubmitUploadFormWithWrongFileType(): void
    {
        [$client, $crawler] = $this->prepareTest('test.pdf');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.form-error-message')->count());
        self::assertSelectorTextContains(
            '.form-error-message',
            'The mime type of the file is invalid ("application/pdf"). Allowed mime types are: "text/plain", "text/csv", "application/csv", "text/x-csv", "application/x-csv", "text/x-comma-separated-values", "text/comma-separated-values"'
        );
    }

    /**
     * @param string $name name of the uploaded file
     *
     * @return array client and Crawler objects
     */
    private function prepareTest(string $name): array
    {
        $client = static::createClient();
        $client->followRedirects();

        $file = $this->getUploadedFile($name);
        $crawler = $client->request('GET', '/upload');
        $form = $crawler->selectButton('Upload')->form();

        $form['upload[file]'] = $file;
        $crawler = $client->submit($form);

        return [$client, $crawler];
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
