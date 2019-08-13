<?php

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\Csv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Class CsvTest.
 */
class CsvTest extends TestCase
{
    public function testFullConfiguration(): void
    {
        $csv = new Csv([
            'columnsCount' => 2,
            'firstLineAsHeader' => true,
            'delimiter' => ';',
        ]);
        $this->assertSame(2, $csv->columnsCount);
        $this->assertTrue($csv->firstLineAsHeader);
        $this->assertSame(';', $csv->delimiter);
    }

    public function testOptionsCanBeSetAfterInitialization(): void
    {
        $csv = new Csv([
            'columnsCount' => 2,
            'firstLineAsHeader' => true,
            'delimiter' => ';',
        ]);

        $csv->columnsCount = 1;
        $csv->firstLineAsHeader = false;
        $csv->delimiter = ',';

        $this->assertSame(1, $csv->columnsCount);
        $this->assertFalse($csv->firstLineAsHeader);
        $this->assertSame(',', $csv->delimiter);
    }

    public function testColumnsCount(): void
    {
        $csv = new Csv(['columnsCount' => 2]);
        $this->assertSame(2, $csv->columnsCount);
    }

    public function testEmptyDelimiter(): void
    {
        $csv = new Csv([
            'columnsCount' => 2,
            'delimiter' => null,
        ]);
        $this->assertSame(';', $csv->delimiter);
    }

    /**
     * @dataProvider provideInvalidColumnsCount
     *
     * @param $columnsCount
     */
    public function testInvalidValueForColumnsCountThrowsException($columnsCount): void
    {
        $this->expectException(ValidatorException::class);
        new Csv(['columnsCount' => $columnsCount]);
    }

    public function testEmptyOptions(): void
    {
        $this->expectException(MissingOptionsException::class);
        new Csv();
    }

    public function testInvalidValueForFirstLineAsHeaderThrowsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new Csv([
            'columnsCount' => 1,
            'firstLineAsHeader' => 'some-string',
        ]);
    }

    public function testInvalidValueForDelimiterThrowsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new Csv([
            'columnsCount' => 1,
            'delimiter' => ';,',
        ]);
    }

    public function provideInvalidColumnsCount(): array
    {
        return [
            ['+100'],
            [-1],
            [0],
            [null],
        ];
    }
}
