<?php

namespace App\DataFixtures;

use App\Entity\Breed;
use App\Entity\Herds;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class HerdsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('pl_PL');

        for ($i = 0; $i < 50; $i++) {
            $herd = new Herds();
            $herd->setName('K' . $faker->numberBetween(1, 10));
            $herd->setHatchingDate($faker->dateTime);
            $breeds = $manager->getRepository(Breed::class)->findAll();
            $herd->setBreed($breeds[rand(0, 1)]);
            $breeders = $manager->getRepository(Supplier::class)->findAll();
            $herd->setBreeder($breeders[rand(0, 19)]);
            $manager->persist($herd);
        }
        
        $manager->flush();
    }
}
