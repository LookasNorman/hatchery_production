<?php

namespace App\DataFixtures;

use App\Entity\Hatchers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HatchersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i < 21; $i++) {
            $hatcher = new Hatchers();
            $hatcher->setName('Aparat klujnkowy ' . $i);
            $hatcher->setShortname('KK' . $i);
            $manager->persist($hatcher);
        }

        $manager->flush();
    }
}
