<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Trait FunctionalTestCaseUtilsTrait.
 */
trait FunctionalTestCaseUtilsTrait
{
    /**
     * Run the schema create tool using our entity metadata.
     *
     * @param EntityManager $entityManager
     */
    protected function createSchema(EntityManager $entityManager): void
    {
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Run the schema update tool using our entity metadata.
     *
     * @param EntityManager $entityManager
     */
    protected function updateSchema(EntityManager $entityManager): void
    {
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Drop database with schema tool.
     *
     * @param EntityManager $entityManager
     */
    protected function dropDatabase(EntityManager $entityManager): void
    {
        (new SchemaTool($entityManager))->dropDatabase();
    }
}
