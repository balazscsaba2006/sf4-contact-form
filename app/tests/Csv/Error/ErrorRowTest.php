<?php

namespace App\Tests\Csv\Error;

use App\Csv\Error\ErrorRow;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class ErrorRowTest.
 */
class ErrorRowTest extends TestCase
{
    public function testInstantiation(): void
    {
        $dto = new ErrorRow(1, 'email', ['This field is invalid.']);

        $this->assertSame(1, $dto->getRowNumber());
        $this->assertSame('email', $dto->getField());
        $this->assertSame(['This field is invalid.'], $dto->getMessages());

        $this->assertEquals('1', (string) $dto);
    }

    public function testPropertiesCannotBeSetAfterInstantiation(): void
    {
        $this->expectException(\RuntimeException::class);

        $dto = new ErrorRow(1, 'email', ['This field is invalid.']);
        $dto->rowNumber = 2;
        $dto->field = 'another_field';
        $dto->messages = ['Updated messages'];
    }

    public function testPropertiesCannotBeRetrievedWithMagicGetter(): void
    {
        $this->expectException(\RuntimeException::class);

        $dto = new ErrorRow(1, 'email', ['This field is invalid.']);
        $rowNum = $dto->rowNumber;
    }

    public function testRestrictedPropertiesExistenceCannotBeChecked(): void
    {
        $this->expectException(\RuntimeException::class);

        $dto = new ErrorRow(1, 'email', ['This field is invalid.']);
        $isset = isset($dto->rowNumber);
    }
}
