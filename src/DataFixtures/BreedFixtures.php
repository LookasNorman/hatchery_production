<?php

namespace App\DataFixtures;

use App\Entity\Breed;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BreedFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $breeds = [
            ['name' => 'Ross', 'type' => '308'],
            ['name' => 'Cobb', 'type' => '500']
        ];

        foreach ($breeds as $breedData) {
            $breed = new Breed();
            $breed->setName($breedData['name']);
            $breed->setType($breedData['type']);
            $manager->persist($breed);
        }

        $manager->flush();
    }
}
