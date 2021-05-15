<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SupplierFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('pl_PL');
        for ($i = 0; $i < 20; $i++) {
            $supplier = new Supplier();
            $supplier->setName($faker->company);
            $supplier->setEmail($faker->companyEmail);
            $supplier->setPhoneNumber($faker->phoneNumber);
            $manager->persist($supplier);
        }
        $manager->flush();
    }
}
