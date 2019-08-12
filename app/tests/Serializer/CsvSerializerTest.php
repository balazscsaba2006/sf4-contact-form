<?php

namespace App\Serializer;

use App\Tests\TestCaseUtilsTrait;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Serializer\Encoder\ChainEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CsvSerializerTest.
 */
class CsvSerializerTest extends TestCase
{
    use TestCaseUtilsTrait;

    public function testInstance(): void
    {
        $serializer = $this->getSerializer();

        $this->assertInstanceOf(Serializer::class, $serializer);

        // assert Encoders
        $reflector = new \ReflectionClass(CsvSerializer::class);
        $encoderReflection = $reflector->getProperty('encoder');
        $encoderReflection->setAccessible(true);
        $encoder = $encoderReflection->getValue($this->getSerializer());
        $this->assertInstanceOf(ChainEncoder::class, $encoder);

        // assert CSV encoder
        $chainReflector = new \ReflectionClass(ChainEncoder::class);
        $chainEncoderReflection = $chainReflector->getProperty('encoders');
        $chainEncoderReflection->setAccessible(true);
        $encoders = $chainEncoderReflection->getValue($encoder);
        $this->assertInstanceOf(CsvEncoder::class, $encoders[0]);
        $this->assertCount(1, $encoders);
    }

    /**
     * @dataProvider getSerializableCsv
     *
     * @param array $data
     * @param string $expected
     */
    public function testCsvSerializeDeserialize(array $data, string $expected): void
    {
        $serializer = $this->getSerializer();
        $encoded = $serializer->serialize($data, 'csv');

        $this->assertEquals($expected, $encoded);
        $decoded = $serializer->decode($encoded, 'csv');

        $this->assertEquals($data, $decoded);
    }

    /**
     * @return array
     */
    public function getSerializableCsv(): array
    {
        return [
            // simple array
            [
                [
                    'foo' => 'aaa',
                    'bar' => 'bbb'
                ],
                $a = <<<PHPEOL
foo,bar
aaa,bbb

PHPEOL
            ],
            // nested array
            [
                [
                    'foo' => 'aaa',
                    'bar' => [
                        ['id' => 111, 1 => 'bbb'],
                        ['lorem' => 'ipsum'],
                    ]
                ],
                $a = <<<PHPEOL
foo,bar.0.id,bar.0.1,bar.1.lorem
aaa,111,bbb,ipsum

PHPEOL
            ],
        ];
    }

    /**
     * @return CsvSerializer
     */
    protected function getSerializer(): CsvSerializer
    {
        return new CsvSerializer();
    }
}
