<?php

namespace Repository;

use App\DataFixtures\LegacyDataFixtures;
use App\Entity\LegacyData;
use App\Repository\LegacyDataRepository;
use App\Tests\FixtureAwareTestCase;
use Doctrine\ORM\EntityManager;

/**
 * Class LegacyDataRepositoryFunctionalTest.
 */
class LegacyDataRepositoryFunctionalTest extends FixtureAwareTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LegacyDataRepository
     */
    private $legacyDataRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->legacyDataRepository = $this->entityManager->getRepository(LegacyData::class);

        $this->createSchema($this->entityManager);

        $this->addFixture(new LegacyDataFixtures());
        $this->executeFixtures();
    }

    public function testFindAll(): void
    {
        $results = $this->legacyDataRepository->findAll();
        $this->assertCount(5, $results);

        /** @var LegacyData $fifth */
        $fifth = $results[4];
        $this->assertEquals(5, $fifth->getId());
        $this->assertEquals('email5@fixture.com', $fifth->getEmail());
        $this->assertEquals('Message 5', $fifth->getMessage());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropDatabase($this->entityManager);
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
