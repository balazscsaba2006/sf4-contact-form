<?php

namespace App\Validator\Constraints;

use App\Serializer\CsvSerializer;
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
     * @var CsvSerializer
     */
    private $serializer;

    /**
     * CsvValidator constructor.
     *
     * @param CsvSerializer $serializer
     */
    public function __construct(CsvSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
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

        $content = file_get_contents($value->getPathname());
        $decoded = $this->serializer->decode($content, 'csv');

        $columnsFound = \count($decoded[0] ?? []);

        if ($constraint->columnsCount !== $columnsFound) {
            $this->context->buildViolation($constraint->invalidColumnsCount)
                ->setParameter('{{ columns }}', $constraint->columnsCount)
                ->setParameter('{{ columns_found }}', $columnsFound)
                ->setPlural((int) $constraint->columnsCount)
                ->setCode(Csv::COLUMNS_COUNT_ERROR)
                ->addViolation();
        }
    }
}
