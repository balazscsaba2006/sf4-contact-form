<?php

namespace App\Tests\Csv;

use App\Csv\Error\ErrorBag;
use App\Csv\Result;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class ResultTest.
 */
class ResultTest extends TestCase
{
    public function testInstantiation(): void
    {
        $result = new Result();
        $this->assertEquals(0, $result->countErrors());
    }

    public function testOperations(): void
    {
        $result = new Result();

        $loops = 5;
        for ($i = 0; $i < $loops; ++$i) {
            $rowNumber = $i + 1;
            $result->addError($rowNumber, 'email', ['This field is invalid.']);
            $result->incrementSaved();
        }
        $this->assertEquals($loops, $result->countSaved());
        $this->assertEquals($loops, $result->countErrors());

        $this->assertInstanceOf(ErrorBag::class, $result->getErrors());
        $this->assertEquals(1, $result->getErrors()->get(1)->getRowNumber());

        $this->assertEquals('Error(s) on row #1 field email: This field is invalid.', $result->getErrorsAsArray()[0]);
        $this->assertCount(5, $result->getErrorsAsArray());
    }
}
