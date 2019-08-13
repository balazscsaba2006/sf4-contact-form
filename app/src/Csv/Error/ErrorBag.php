<?php

namespace App\Csv\Error;

/**
 * Class ErrorBag.
 */
class ErrorBag implements \IteratorAggregate, \Countable
{
    /**
     * Error storage.
     */
    protected $errors;

    /**
     * @param array $errors An array of errors
     */
    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
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
     * @return array An array of errors
     */
    public function all(): array
    {
        return $this->errors;
    }

    /**
     * Returns the error keys.
     *
     * @return array An array of error keys
     */
    public function keys(): array
    {
        return array_keys($this->errors);
    }

    /**
     * Adds errors.
     *
     * @param array $errors An array of errors
     */
    public function add(array $errors = [])
    {
        $this->errors = array_replace($this->errors, $errors);
    }

    /**
     * Returns an error by name.
     *
     * @param string $key     The key
     * @param mixed  $default The default value if the error key does not exist
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
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @return self
     */
    public function set($key, $value): self
    {
        $this->errors[$key] = $value;

        return $this;
    }

    /**
     * Returns true if the error is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the error exists, false otherwise
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->errors);
    }

    /**
     * Removes an error.
     *
     * @param string $key The key
     */
    public function remove($key)
    {
        unset($this->errors[$key]);
    }

    /**
     * Returns an iterator for errors.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->errors);
    }

    /**
     * Returns the number of errors.
     *
     * @return int The number of errors
     */
    public function count(): int
    {
        return \count($this->errors);
    }
}
