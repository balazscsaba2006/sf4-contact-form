<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CsvSerializer.
 */
class CsvSerializer extends Serializer
{
    /**
     * CsvSerializer constructor.
     */
    public function __construct()
    {
        parent::__construct([new ObjectNormalizer()], [new CsvEncoder()]);
    }
}
