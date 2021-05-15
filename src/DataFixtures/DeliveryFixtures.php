<?php

namespace App\DataFixtures;

use App\Entity\Delivery;
use App\Entity\Herds;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DeliveryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('pl_PL');

        for ($i = 1; $i < 210; $i++) {
            $delivery = new Delivery();
            $eggsNumber = rand(1, 3) * 4800;
            $delivery->setEggsNumber($eggsNumber);
            $delivery->setEggsOnWarehouse($eggsNumber);
            $delivery->setDeliveryDate($faker->dateTimeBetween('-4 months', 'now'));
            $delivery->setFirstLayingDate($faker->dateTimeBetween('-4 months', 'now'));
            $delivery->setLastLayingDate($faker->dateTimeBetween('-4 months', 'now'));
            $herds = $manager->getRepository(Herds::class)->findAll();
            $herdsCount = count($herds) - 1;
            $delivery->setHerd($herds[rand(0, $herdsCount)]);
            $manager->persist($delivery);
        }

        $manager->flush();
    }
}
