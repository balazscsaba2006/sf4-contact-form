<?php

namespace App\Tests\Csv\Error;

use App\Csv\Error\ErrorBag;
use App\Csv\Error\ErrorRow;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class ErrorBagTest.
 */
class ErrorBagTest extends TestCase
{
    public function testInstantiation(): void
    {
        $rowNumber = 1;
        $init = [$rowNumber => new ErrorRow($rowNumber, 'email', ['This field is invalid.'])];
        $bag = new ErrorBag($init);

        $this->assertInstanceOf(ErrorBag::class, $bag);
        $this->assertSame($init, $bag->all());
        $this->assertSame(array_keys($init), $bag->keys());
        $this->assertSame($init[$rowNumber], $bag->get($rowNumber));
        $this->assertSame(\count($init), $bag->count());
        $this->assertEquals(new \ArrayIterator($init), $bag->getIterator());
    }

    public function testOperations(): void
    {
        $init = [1 => new ErrorRow(1, 'email', ['This field is invalid.'])];
        $bag = new ErrorBag($init);

        $addRow = new ErrorRow(2, 'message', ['The message is too long.']);
        $bag->add($addRow);
        $this->assertSame($addRow, $bag->get(2));

        // new row number adds to the stack
        $setRow = new ErrorRow(3, 'email', ['This field is invalid.']);
        $bag->set($setRow);
        $this->assertEquals(3, $bag->count());
        $this->assertSame($setRow, $bag->get(3));

        // same row number updates the error
        $rowBeforeUpdate = $bag->get(3);
        $updateRow = new ErrorRow(3, 'message', ['The message is too long.']);
        $bag->set($updateRow);
        $this->assertEquals(3, $bag->count());
        $this->assertSame($updateRow, $bag->get(3));
        $this->assertNotEquals($rowBeforeUpdate->getField(), $bag->get(3)->getField());
        $this->assertNotEquals($rowBeforeUpdate->getMessages(), $bag->get(3)->getMessages());

        $bag->remove(3);
        $this->assertNull($bag->get(3));
        $this->assertFalse($bag->has(3));

        $bag->addRow(3, 'email', ['This field is invalid.']);
        $this->assertEquals(3, $bag->count());
        $this->assertSame(3, $bag->get(3)->getRowNumber());

        $this->assertEquals([
            'Error(s) on row #1 field email: This field is invalid.',
            'Error(s) on row #2 field message: The message is too long.',
            'Error(s) on row #3 field email: This field is invalid.',
        ], $bag->toArray());
    }

    public function testMagicMethodsAfterInstantiation(): void
    {
        $rowNumber = 1;
        $init = [$rowNumber => new ErrorRow(1, 'email', ['This field is invalid.'])];
        $bag = new ErrorBag($init);

        $row = $bag->{$rowNumber};
        $this->assertSame($init[$rowNumber], $row);
        $this->assertTrue(isset($bag->{$rowNumber}));
    }

    public function testErrorsCannotBeSetViaMagicSetter(): void
    {
        $this->expectException(\RuntimeException::class);

        $rowNumber = 1;
        $init = [$rowNumber => new ErrorRow(1, 'email', ['This field is invalid.'])];
        $bag = new ErrorBag($init);

        $bag->{$rowNumber} = new ErrorRow(2, 'message', ['The message is too long.']);
    }
}
