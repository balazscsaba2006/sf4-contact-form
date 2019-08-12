<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait WebTestCaseControllerTrait.
 */
trait WebTestCaseControllerTrait
{
    /**
     * @param string $name
     *
     * @return UploadedFile
     */
    public function getUploadedFile(string $name): UploadedFile
    {
        return new UploadedFile(
            __DIR__ .'/../data/' . $name,
            $name,
            'text/plain',
            null,
            true
        );
    }
}
