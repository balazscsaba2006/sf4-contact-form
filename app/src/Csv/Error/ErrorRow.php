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
    private $field;

    /**
     * @var string[]|array
     */
    private $messages;

    /**
     * ErrorRow constructor.
     *
     * @param int            $rowNumber
     * @param string         $field
     * @param string[]|array $messages
     */
    public function __construct(int $rowNumber, string $field, array $messages)
    {
        $this->rowNumber = $rowNumber;
        $this->field = $field;
        $this->messages = $messages;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->rowNumber;
    }

    /**
     * @param string $name
     */
    public function __get(string $name): void
    {
        throw new \RuntimeException(sprintf('Method %s is not allowed.', __METHOD__));
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value): void
    {
        throw new \RuntimeException(sprintf('Method %s is not allowed.', __METHOD__));
    }

    /**
     * @param string $name
     */
    public function __isset(string $name): void
    {
        throw new \RuntimeException(sprintf('Method %s is not allowed.', __METHOD__));
    }
}
