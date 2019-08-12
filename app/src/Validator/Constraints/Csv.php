<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class Csv.
 *
 * @Annotation
 */
class Csv extends Constraint
{
    public const COLUMNS_COUNT_ERROR = 'aff8e338-6ae9-4cdf-b822-50e5b2579e69';

    public $invalidColumnsCount = 'Each line must contain exactly {{ columns }} columns, {{ columns_found }} columns found.';
    public $columnsCount;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->columnsCount) {
            throw new MissingOptionsException(sprintf('Option "columnsCount" must be given for constraint %s', __CLASS__), ['columnsCount']);
        }
    }
}
