<?php

namespace App\DataFixtures;

use App\Entity\Inputs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InputsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i < 71; $i++) {
            $input = new Inputs();
            $input->setName('N' . $i);
            $input->setInputDate($faker->dateTimeBetween('-4 months', 'now'));
            $manager->persist($input);
        }

        $manager->flush();
    }
}
