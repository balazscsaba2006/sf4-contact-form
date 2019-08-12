<?php

namespace App\DataFixtures;

use App\Entity\LegacyData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LegacyDataFixtures.
 */
class LegacyDataFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $data = new LegacyData();
            $data->setEmail(sprintf('email%d@fixture.com', $i));
            $data->setMessage(sprintf('Message %d', $i));
            $manager->persist($data);
        }

        $manager->flush();
    }
}
