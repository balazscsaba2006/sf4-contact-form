<?php

namespace App\Validator\Constraints;

use App\Csv\HandlerInterface;
use League\Csv\Exception as CsvException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Class CsvValidator.
 */
class CsvValidator extends ConstraintValidator
{
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * CsvValidator constructor.
     *
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     *
     * @throws CsvException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Csv) {
            throw new UnexpectedTypeException($constraint, Csv::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof UploadedFile) {
            throw new UnexpectedValueException($value, UploadedFile::class);
        }

        $csv = $this->handler->parse($value->getPathname());
        $csv->setDelimiter($constraint->delimiter);

        if (true === $constraint->firstLineAsHeader) {
            $csv->setHeaderOffset(0);
        }

        $firstLine = $this->handler->getFirstLine($csv);
        $columnsFound = \count($firstLine);

        if ($constraint->columnsCount !== $columnsFound) {
            $this->context->buildViolation($constraint->invalidColumnsCountMessage)
                ->setParameter('{{ columns }}', $constraint->columnsCount)
                ->setParameter('{{ columns_found }}', $columnsFound)
                ->setPlural((int) $constraint->columnsCount)
                ->setCode(Csv::COLUMNS_COUNT_ERROR)
                ->addViolation();

            return;
        }

        $firstRecord = $this->handler->getFirstRecord($csv);
        if (empty($firstRecord)) {
            $this->context->buildViolation($constraint->noRecordsMessage)
                ->setCode(Csv::NO_RECORDS_ERROR)
                ->addViolation();
        }
    }
}
