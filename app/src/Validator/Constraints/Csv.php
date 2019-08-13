<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * Class Csv.
 *
 * @Annotation
 */
class Csv extends Constraint
{
    public const COLUMNS_COUNT_ERROR = 'ea50768c-aacd-4461-8b8c-f51d1582d928';
    public const NO_RECORDS_ERROR = '65949812-abc5-4608-b9d6-c4d930dace72';

    public $invalidColumnsCountMessage = 'Each line must contain exactly {{ columns }} column(s), {{ columns_found }} column(s) found.';
    public $noRecordsMessage = 'File contains no records.';

    public const DEFAULT_DELIMITER = ';';

    /**
     * @var int
     */
    public $columnsCount;

    /**
     * @var bool
     */
    public $firstLineAsHeader = true;

    /**
     * @var string
     */
    public $delimiter = self::DEFAULT_DELIMITER;

    /**
     * Csv constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (null !== $options
            && array_key_exists('delimiter', $options)
            && null === $options['delimiter']) {
            $options['delimiter'] = self::DEFAULT_DELIMITER;
        }

        parent::__construct($options);

        $this->assertColumnsCountValidity();
        $this->assertFirstLineAsHeaderValidity();
        $this->assertDelimiterValidity();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return [
            'columnsCount',
        ];
    }

    /**
     * Checks "columnsCount" option validity.
     */
    private function assertColumnsCountValidity(): void
    {
        if (!is_int($this->columnsCount)) {
            throw new InvalidOptionsException(sprintf(
                'Option "columnsCount" must be of type "integer" for constraint %s, "%s" given.',
                __CLASS__,
                \gettype($this->columnsCount)
            ), ['columnsCount']);
        }

        if ($this->columnsCount < 1) {
            throw new InvalidOptionsException(sprintf(
                'Option "columnsCount" must be greater than 1 for constraint %s, %d given.',
                __CLASS__,
                $this->columnsCount
            ), ['columnsCount']);
        }
    }

    /**
     * Checks "firstLineAsHeader" option validity.
     */
    private function assertFirstLineAsHeaderValidity(): void
    {
        if (!is_bool($this->firstLineAsHeader)) {
            throw new InvalidOptionsException(sprintf(
                'Option "firstLineAsHeader" must be of type "boolean" for constraint %s, "%s" given.',
                __CLASS__,
                \gettype($this->firstLineAsHeader)
            ), ['firstLineAsHeader']);
        }
    }

    /**
     * Checks "delimiter" option validity.
     */
    private function assertDelimiterValidity(): void
    {
        if (1 !== strlen($this->delimiter)) {
            throw new InvalidOptionsException(sprintf(
                'Option "delimiter" for constraint %s is expected to be a single character, %s given.',
                __CLASS__,
                $this->delimiter
            ), ['delimiter']);
        }
    }
}
