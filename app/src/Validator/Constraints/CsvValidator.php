<?php

namespace App\Validator\Constraints;

use League\Csv\Exception as CsvException;
use League\Csv\Reader;
use League\Csv\Statement;
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

        $csv = Reader::createFromPath($value->getPathname(), 'r');
        $csv->setDelimiter($constraint->delimiter);

        if (true === $constraint->firstLineAsHeader) {
            $csv->setHeaderOffset(0);
        }

        $firstLine = $this->getCsvFirstLine($constraint, $csv);
        $columnsFound = \count($firstLine);

        if ($constraint->columnsCount !== $columnsFound) {
            $this->context->buildViolation($constraint->invalidColumnsCountMessage)
                ->setParameter('{{ columns }}', $constraint->columnsCount)
                ->setParameter('{{ columns_found }}', $columnsFound)
                ->setPlural((int) $constraint->columnsCount)
                ->setCode(Csv::COLUMNS_COUNT_ERROR)
                ->addViolation();
        }

        $firstRecord = $this->getCsvFirstRecord($csv);
        if (empty($firstRecord)) {
            $this->context->buildViolation($constraint->noRecordsMessage)
                ->setCode(Csv::NO_RECORDS_ERROR)
                ->addViolation();
        }
    }

    /**
     * @param Csv    $constraint
     * @param Reader $csv
     *
     * @return array|mixed|string[]
     */
    private function getCsvFirstLine(Csv $constraint, Reader $csv)
    {
        try {
            if (true === $constraint->firstLineAsHeader) {
                return $csv->getHeader();
            }

            return $this->getCsvFirstRecord($csv);
        } catch (CsvException $e) {
            return [];
        }
    }

    /**
     * @param Reader $csv
     *
     * @return array|mixed|string[]
     */
    private function getCsvFirstRecord(Reader $csv)
    {
        try {
            $stmt = (new Statement())
                ->offset(0)
                ->limit(1);

            $result = $stmt->process($csv);

            return $result->fetchOne(0);
        } catch (CsvException $e) {
            return [];
        }
    }
}
