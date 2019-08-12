<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class FixtureAwareTestCase.
 */
abstract class FixtureAwareTestCase extends KernelTestCase
{
    /**
     * @var ORMExecutor
     */
    private $fixtureExecutor;

    /**
     * @var ContainerAwareLoader
     */
    private $fixtureLoader;

    protected function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * Adds a new fixture to be loaded.
     *
     * @param FixtureInterface $fixture
     */
    protected function addFixture(FixtureInterface $fixture): void
    {
        $this->getFixtureLoader()->addFixture($fixture);
    }

    /**
     * Executes all the fixtures that have been loaded so far.
     */
    protected function executeFixtures(): void
    {
        $this->getFixtureExecutor()->execute(
            $this->getFixtureLoader()->getFixtures()
        );
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

    /**
     * Get the class responsible for loading the data fixtures.
     * This will also load in the ORM Purger which purges the database before loading in the data fixtures.
     */
    private function getFixtureExecutor(): ORMExecutor
    {
        if (!$this->fixtureExecutor) {
            /** @var EntityManager $entityManager */
            $entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
            $this->fixtureExecutor = new ORMExecutor($entityManager, new ORMPurger($entityManager));
        }

        return $this->fixtureExecutor;
    }

    /**
     * Get the Doctrine data fixtures loader.
     */
    private function getFixtureLoader(): ContainerAwareLoader
    {
        if (!$this->fixtureLoader) {
            $this->fixtureLoader = new ContainerAwareLoader(static::$kernel->getContainer());
        }

        return $this->fixtureLoader;
    }
}
