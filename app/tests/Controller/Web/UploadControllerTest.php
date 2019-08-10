<?php

namespace App\Tests\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    /**
     * Tests submitting the upload form
     */
    public function testSubmitUploadForm()
    {
        $client = static::createClient();
        $client->followRedirects();

        $file = new UploadedFile(
            __DIR__ .'/../../data/correct.csv',
            'correct.csv',
            'text/plain',
            null,
            true
        );
        $crawler = $client->request('GET', '/upload');
        $form = $crawler->selectButton('Upload')->form();

        $form['upload[file]'] = $file;
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            1,
            $crawler->filter('div.alert.alert-success')->count()
        );
        self::assertSelectorTextContains('div.alert.alert-success', 'Nice job! The uploaded file was successfully saved.');
    }
}
