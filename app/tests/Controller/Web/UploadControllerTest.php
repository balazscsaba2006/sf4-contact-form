<?php

namespace App\Tests\Controller\Web;

use App\Tests\WebTestCaseUtilsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UploadControllerTest.
 */
class UploadControllerTest extends WebTestCase
{
    use WebTestCaseUtilsTrait;

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
     * Tests successfully submitting the upload form.
     */
    public function testSubmitUploadFormSuccessfully(): void
    {
        [$client, $crawler] = $this->prepareTest('correct.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
        self::assertSelectorTextContains('div.alert.alert-success', 'Nice job! The uploaded file was successfully saved.');
    }

    /**
     * Tests submitting the upload form with too large CSV.
     */
    public function testSubmitUploadFormWithTooLargeCsv(): void
    {
        [$client, $crawler] = $this->prepareTest('too_large.csv');

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
        $this->markTestSkipped('must be revisited.');

        [$client, $crawler] = $this->prepareTest('incorrect.csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertEquals(1, $crawler->filter('.form-error-message')->count());
//        self::assertSelectorTextContains(
//            '.form-error-message',
//            'The mime type of the file is invalid ("application/pdf"). Allowed mime types are: "text/plain", "text/csv", "application/csv", "text/x-csv", "application/x-csv", "text/x-comma-separated-values", "text/comma-separated-values"'
//        );
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
}
