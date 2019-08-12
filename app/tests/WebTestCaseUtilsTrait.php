<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait WebTestCaseUtilsTrait.
 */
trait WebTestCaseUtilsTrait
{
    /**
     * @param string $name
     *
     * @return UploadedFile
     */
    public function getUploadedFile(string $name): UploadedFile
    {
        return new UploadedFile(
            __DIR__.'/.data/'.$name,
            $name,
            'text/plain',
            null,
            true
        );
    }
}
