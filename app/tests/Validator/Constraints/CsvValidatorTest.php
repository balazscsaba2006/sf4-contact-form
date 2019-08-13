<?php

namespace App\Tests\Validator\Constraints;

use App\Tests\WebTestCaseUtilsTrait;
use App\Validator\Constraints\Csv;
use App\Validator\Constraints\CsvValidator;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class CsvValidatorTest.
 */
class CsvValidatorTest extends ConstraintValidatorTestCase
{
    use WebTestCaseUtilsTrait;

    /**
     * @return CsvValidator
     */
    protected function createValidator(): CsvValidator
    {
        return new CsvValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Csv(['columnsCount' => 2]));
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Csv(['columnsCount' => 2]));
        $this->assertNoViolation();
    }

    public function testValidUploadedFile(): void
    {
        $file = $this->getUploadedFile('correct.csv');
        $this->validator->validate($file, new Csv(['columnsCount' => 2]));

        $this->assertNoViolation();
    }

    public function testExpectsUploadedFileInstanceAsValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new Csv(['columnsCount' => 2]));
    }

    public function testExpectsCsvInstanceAsConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $file = $this->getUploadedFile('correct.csv');
        $this->validator->validate($file, new File());
    }

    public function testBlankUploadedFileWithFirstLineAsHeader(): void
    {
        $file = $this->getUploadedFile('blank.csv');
        $this->validator->validate($file, new Csv(['columnsCount' => 2]));

        // expect violations for invalid column count and empty records
        $this->assertSame(2, $violationsCount = \count($this->context->getViolations()));
    }

    public function testBlankUploadedFileWithoutHeader(): void
    {
        $file = $this->getUploadedFile('blank.csv');
        $this->validator->validate($file, new Csv([
            'columnsCount' => 2,
            'firstLineAsHeader' => false,
        ]));

        // expect violations for invalid column count and empty records
        $this->assertSame(2, $violationsCount = \count($this->context->getViolations()));
    }
}
