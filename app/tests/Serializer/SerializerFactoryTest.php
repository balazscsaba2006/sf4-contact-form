<?php

namespace App\Serializer;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SerializerFactoryTest.
 */
class SerializerFactoryTest extends TestCase
{
    public function testCreateCsvSerializer(): void
    {
        $factory = new SerializerFactory();

        $this->assertInstanceOf(Serializer::class, $factory->createCsvSerializer());
    }
}
