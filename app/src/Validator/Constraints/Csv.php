<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

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
        parent::__construct($options);

        if (array_key_exists('delimiter', $options) && null === $options['delimiter']) {
            $options['delimiter'] = self::DEFAULT_DELIMITER;
        }

        if (null === $this->columnsCount) {
            throw new MissingOptionsException(sprintf(
                'Option "columnsCount" must be given for constraint %s',
                __CLASS__
            ), ['columnsCount']);
        }

        if (!is_int($this->columnsCount)) {
            throw new InvalidOptionsException(sprintf(
                'Option "columnsCount" must be of type "string" for constraint %s, "%s" given.',
                __CLASS__,
                \gettype($this->columnsCount)
            ), ['columnsCount']);
        }

        if (!is_bool($this->firstLineAsHeader)) {
            throw new InvalidOptionsException(sprintf(
                'Option "firstLineAsHeader" must be of type "boolean" for constraint %s, "%s" given.',
                __CLASS__,
                \gettype($this->firstLineAsHeader)
            ), ['firstLineAsHeader']);
        }

        if (1 !== strlen($this->delimiter)) {
            throw new InvalidOptionsException(sprintf(
                'Option "delimiter" for constraint %s is expected to be a single character, %s given.',
                __CLASS__,
                $this->delimiter
            ), ['delimiter']);
        }
    }
}
