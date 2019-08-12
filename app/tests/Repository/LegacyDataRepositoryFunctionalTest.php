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

        $kernel = static::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->legacyDataRepository = $this->entityManager->getRepository(LegacyData::class);

        $this->addFixture(new LegacyDataFixtures());
        $this->executeFixtures();
    }

    public function testFindAll(): void
    {
        $results = $this->legacyDataRepository->findAll();
        $this->assertCount(5, $results);

        $this->assertEquals('email5@fixture.com', $results[4]->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
