<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait WebTestCaseUtilsTrait.
 */
trait WebTestCaseUtilsTrait
{
    use TestCaseUtilsTrait;

    /**
     * @param string $name
     *
     * @return UploadedFile
     */
    public function getUploadedFile(string $name): UploadedFile
    {
        return new UploadedFile(
            $this->getPathToFile($name),
            $name,
            'text/plain',
            null,
            true
        );
    }
}
