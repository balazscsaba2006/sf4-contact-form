<?php

namespace App\Csv;

use App\Csv\Error\ErrorBag;

/**
 * Class Result.
 */
class Result
{
    /**
     * @var int
     */
    private $saved = 0;

    /**
     * @var ErrorBag
     */
    private $errors;

    /**
     * Result constructor.
     */
    public function __construct()
    {
        $this->errors = new ErrorBag();
    }

    /**
     * @return self
     */
    public function incrementSaved(): self
    {
        ++$this->saved;

        return $this;
    }

    /**
     * @param int    $rowNumber
     * @param string $field
     * @param array  $messages
     *
     * @return Result
     */
    public function addError(int $rowNumber, string $field, array $messages): self
    {
        $this->errors->addRow($rowNumber, $field, $messages);

        return $this;
    }

    /**
     * @return int
     */
    public function countSaved(): int
    {
        return $this->saved;
    }

    /**
     * @return int
     */
    public function countErrors(): int
    {
        return $this->errors->count();
    }

    /**
     * @return ErrorBag
     */
    public function getErrors(): ErrorBag
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getErrorsAsArray(): array
    {
        return $this->errors->toArray();
    }
}
