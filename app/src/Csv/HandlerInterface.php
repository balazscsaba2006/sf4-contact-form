<?php

namespace App\Csv;

use League\Csv\Reader;

/**
 * Interface HandlerInterface.
 */
interface HandlerInterface
{
    /**
     * Parses a CSV file from the supplied $path.
     *
     * @param string $path
     *
     * @return Reader
     */
    public function parse(string $path): Reader;

    /**
     * Gets records from CSV file.
     *
     * @param Reader $csv
     *
     * @return \Iterator
     */
    public function getRecords(Reader $csv): \Iterator;

    /**
     * Validates rows of a CSV file.
     *
     * @param \Iterator $rows
     */
    public function validate(\Iterator $rows): void;

    /**
     * @param Reader $csv
     *
     * @return array|mixed|string[]
     */
    public function getHeader(Reader $csv);

    /**
     * @param Reader $csv
     *
     * @return array|mixed|string[]
     */
    public function getFirstLine(Reader $csv);

    /**
     * @param Reader $csv
     *
     * @return array|mixed|string[]
     */
    public function getFirstRecord(Reader $csv);
}
