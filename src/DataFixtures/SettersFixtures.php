<?php

namespace App\DataFixtures;

use App\Entity\Setters;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i < 21; $i++) {
            $setter = new Setters();
            $setter->setName('Aparat lęgowy ' . $i);
            $setter->setShortname('KL' . $i);
            $manager->persist($setter);
        }

        $manager->flush();
    }
}
