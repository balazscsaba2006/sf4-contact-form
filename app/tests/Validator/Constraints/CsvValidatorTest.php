<?php

namespace App\Tests\Validator\Constraints;

use App\Csv\CsvHandler;
use App\Tests\WebTestCaseUtilsTrait;
use App\Validator\Constraints\Csv;
use App\Validator\Constraints\CsvValidator;
use PHPUnit\Framework\MockObject\MockObject;
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

    private function getCsvHandlerMock(): MockObject
    {
        return $this->getMockBuilder(CsvHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return CsvValidator
     */
    protected function createValidator(): CsvValidator
    {
        return new CsvValidator($this->getCsvHandlerMock());
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
        $csvHandlerMock = $this->getCsvHandlerMock();
        $csvHandlerMock->expects($this->once())
            ->method('getFirstLine')
            ->willReturn(['email', 'message']);
        $csvHandlerMock->expects($this->once())
            ->method('getFirstRecord')
            ->willReturn(['gboss@live.com', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.']);

        $validator = new CsvValidator($csvHandlerMock);

        $file = $this->getUploadedFile('correct.csv');
        $validator->validate($file, new Csv(['columnsCount' => 2]));

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
        $csvHandlerMock = $this->getCsvHandlerMock();
        $csvHandlerMock->expects($this->once())
            ->method('getFirstLine')
            ->willReturn([]);
        $csvHandlerMock->expects($this->once())
            ->method('getFirstRecord')
            ->willReturn([]);

        $validator = new CsvValidator($csvHandlerMock);

        $file = $this->getUploadedFile('blank.csv');
        $validator->validate($file, new Csv(['columnsCount' => 2]));

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
