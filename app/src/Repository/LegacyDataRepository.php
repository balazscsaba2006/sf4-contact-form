<?php

namespace App\Repository;

use App\Entity\LegacyData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class LegacyDataRepository.
 */
class LegacyDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LegacyData::class);
    }
}
