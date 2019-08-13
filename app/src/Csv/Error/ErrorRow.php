<?php

namespace App\Csv\Error;

/**
 * Class ErrorRow.
 */
class ErrorRow
{
    /**
     * @var int
     */
    private $rowNumber;

    /**
     * @var string
     */
    public $field;

    /**
     * @var string[]|array
     */
    private $messages;

    /**
     * ErrorRow constructor.
     *
     * @param int $rowNumber
     * @param string $field
     * @param string[]|array $messages
     */
    public function __construct(int $rowNumber, string $field, array $messages)
    {
        $this->rowNumber = $rowNumber;
        $this->field = $field;
        $this->messages = $messages;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->rowNumber;
    }

    /**
     * @return int
     */
    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string[]|array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
