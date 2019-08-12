<?php

namespace App\Serializer;

/**
 * Class SerializerFactory.
 */
class SerializerFactory
{
    /**
     * @return CsvSerializer
     */
    public function createCsvSerializer(): CsvSerializer
    {
        return new CsvSerializer();
    }
}
