<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Trait ApiTestCaseUtilsTrait.
 */
class ApiTestCase extends WebTestCase
{
    protected function assertJsonEquals(array $expected, string $actual): void
    {
        $actual = json_encode($this->sortRecursive(
            json_decode($actual, true)
        ));
        $data = json_encode($this->sortRecursive(
            json_decode(json_encode($expected), true)
        ));
        $this->assertEquals($data, $actual);
    }

    /**
     * Assert that the response contains the given JSON.
     *
     * @param array  $expected
     * @param string $actual
     * @param bool   $negate
     */
    protected function assertJsonContains(array $expected, string $actual, $negate = false): void
    {
        $method = $negate ? 'assertFalse' : 'assertTrue';
        $actual = json_decode($actual, true);

        if (null === $actual || false === $actual) {
            $this->fail('Invalid JSON was returned from the route. Perhaps an exception was thrown?');
        }

        $actual = \json_encode($this->sortRecursive((array) $actual));
        foreach ($this->sortRecursive($expected) as $key => $value) {
            $expected = $this->formatToExpectedJson($key, $value);
            $this->{$method}(
                $this->strContains($actual, $expected),
                ($negate ? 'Found unexpected' : 'Unable to find')." JSON fragment [{$expected}] within [{$actual}]."
            );
        }
    }

    /**
     * Assert that the JSON response has a given structure.
     *
     * @param array        $expected
     * @param string|array $actual
     */
    protected function assertJsonStructure(array $expected, $actual): void
    {
        if (!is_array($actual)) {
            $actual = json_decode($actual, true);
        }

        foreach ($expected as $key => $value) {
            if ('*' === $key && \is_array($value)) {
                $this->assertIsArray($actual);
                foreach ($actual as $responseDataItem) {
                    $this->assertJsonStructure($expected['*'], $responseDataItem);
                }
            } elseif (\is_array($value)) {
                $this->assertArrayHasKey($key, $actual);
                $this->assertJsonStructure($expected[$key], $actual[$key]);
            } else {
                $this->assertArrayHasKey($value, $actual);
            }
        }
    }

    /**
     * Format the given key and value into a JSON string for expectation checks.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return string
     */
    private function formatToExpectedJson($key, $value): string
    {
        $expected = \json_encode([$key => $value]);
        if ($this->strStartsWith($expected, '{')) {
            $expected = \substr($expected, 1);
        }
        if ($this->strEndsWith($expected, '}')) {
            $expected = \substr($expected, 0, -1);
        }

        return $expected;
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @param array $array
     *
     * @return array
     */
    private function sortRecursive($array): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::sortRecursive($value);
            }
        }

        if ($this->isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }

        return $array;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param array $array
     *
     * @return bool
     */
    private function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    private function strContains($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== mb_strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    private function strStartsWith($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    private function strEndsWith($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}
