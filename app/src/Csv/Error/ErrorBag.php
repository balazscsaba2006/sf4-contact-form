<?php

namespace App\Csv\Error;

/**
 * Class ErrorBag.
 */
class ErrorBag implements \IteratorAggregate, \Countable
{
    /**
     * Error storage.
     *
     * @param ErrorRow[]|array
     */
    protected $errors;

    /**
     * @param array $errors
     */
    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * @param string $key
     *
     * @return ErrorRow
     */
    public function __get(string $key): ErrorRow
    {
        return $this->get($key);
    }

    /**
     * @param string   $key
     * @param ErrorRow $row
     */
    public function __set(string $key, ErrorRow $row)
    {
        $this->set($key, $row);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * Returns the errors.
     *
     * @return ErrorRow[]|array An array of errors
     */
    public function all(): array
    {
        return $this->errors;
    }

    /**
     * Returns the error keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->errors);
    }

    /**
     * @param int $rowNumber
     * @param string $field
     * @param array $messages
     */
    public function addRow(int $rowNumber, string $field, array $messages)
    {
        $this->add(new ErrorRow($rowNumber, $field, $messages));
    }

    /**
     * Adds errors.
     *
     * @param ErrorRow $row
     */
    public function add(ErrorRow $row)
    {
        $this->errors[(string) $row] = $row;
    }

    /**
     * Returns an error by name.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->errors) ? $this->errors[$key] : $default;
    }

    /**
     * Sets an error by name.
     *
     * @param string   $key
     * @param ErrorRow $row
     *
     * @return self
     */
    public function set($key, ErrorRow $row): self
    {
        $this->errors[$key] = $row;

        return $this;
    }

    /**
     * Returns true if the error is defined.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->errors);
    }

    /**
     * Removes an error.
     *
     * @param string $key
     */
    public function remove($key)
    {
        unset($this->errors[$key]);
    }

    /**
     * Returns an iterator for errors.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->errors);
    }

    /**
     * Returns the number of errors.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->errors);
    }
}
