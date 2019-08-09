<?php

namespace App\Repository;

use App\Entity\LegacyData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LegacyData|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegacyData|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegacyData[]    findAll()
 * @method LegacyData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegacyDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LegacyData::class);
    }

    // /**
    //  * @return LegacyData[] Returns an array of LegacyData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LegacyData
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
