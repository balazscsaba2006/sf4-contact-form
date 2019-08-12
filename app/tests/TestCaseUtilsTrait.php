<?php

namespace App\Tests;

/**
 * Trait TestCaseUtilsTrait.
 */
trait TestCaseUtilsTrait
{
    /**
     * @return string
     */
    public function getDataDir(): string
    {
        return __DIR__.'/.data/';
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getPathToFile(string $filename): string
    {
        return $this->getDataDir().$filename;
    }

    /**
     * @param string $filename
     * @param string $data
     */
    public function writeToFile(string $filename, string $data): void
    {
        file_put_contents($this->getPathToFile($filename), $data);
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function readFromFile(string $filename): string
    {
        return file_get_contents($this->getPathToFile($filename));
    }
}
