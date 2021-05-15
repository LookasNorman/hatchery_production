<?php

namespace App\DataFixtures;

use App\Entity\ChicksRecipient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ChicksRecipientFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('pl_PL');

        for ($i = 0; $i < 20; $i++) {
            $chickRecipient = new ChicksRecipient();
            $chickRecipient->setName($faker->company);
            $chickRecipient->setPhoneNumber($faker->phoneNumber);
            $chickRecipient->setEmail($faker->companyEmail);
            $manager->persist($chickRecipient);
        }

        $manager->flush();
    }
}
