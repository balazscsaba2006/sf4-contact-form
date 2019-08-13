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
        $csvHandler = $this->getCsvHandlerStub(
            ['email', 'message'],
            true,
            ['gboss@live.com', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.']
        );
        $validator = new CsvValidator($csvHandler);

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
        $csvHandler = $this->getCsvHandlerStub([], false);
        $validator = new CsvValidator($csvHandler);
        $context = $this->createContext();
        $validator->initialize($context);

        $file = $this->getUploadedFile('blank.csv');
        $validator->validate($file, new Csv(['columnsCount' => 2]));

        $this->assertSame(1, $violationsCount = \count($context->getViolations()));
    }

    public function testBlankUploadedFileWithFirstLineAsHeaderAndEmptyContent(): void
    {
        $csvHandler = $this->getCsvHandlerStub(
            ['email', 'message'],
            true,
            []
        );
        $validator = new CsvValidator($csvHandler);
        $context = $this->createContext();
        $validator->initialize($context);

        $file = $this->getUploadedFile('empty_content.csv');
        $validator->validate($file, new Csv(['columnsCount' => 2]));

        $this->assertSame(1, $violationsCount = \count($context->getViolations()));
    }

    public function testBlankUploadedFileWithoutHeader(): void
    {
        $csvHandler = $this->getCsvHandlerStub([], false);
        $validator = new CsvValidator($csvHandler);
        $context = $this->createContext();
        $validator->initialize($context);

        $file = $this->getUploadedFile('blank.csv');
        $validator->validate($file, new Csv([
            'columnsCount' => 2,
            'firstLineAsHeader' => false,
        ]));

        $this->assertSame(1, $violationsCount = \count($context->getViolations()));
    }

    private function getCsvHandlerMock(): MockObject
    {
        return $this->getMockBuilder(CsvHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getCsvHandlerStub($firstLineArg, bool $mockFirstRecord = true, $firstRecordArg = null): MockObject
    {
        $csvHandlerMock = $this->getCsvHandlerMock();
        $csvHandlerMock->expects($this->once())
            ->method('getFirstLine')
            ->willReturn($firstLineArg);

        if (true === $mockFirstRecord) {
            $csvHandlerMock->expects($this->once())
                ->method('getFirstRecord')
                ->willReturn($firstRecordArg);
        }

        return $csvHandlerMock;
    }
}
