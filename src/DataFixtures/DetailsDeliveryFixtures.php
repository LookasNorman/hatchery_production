<?php

namespace App\DataFixtures;

use App\Entity\ChicksRecipient;
use App\Entity\Delivery;
use App\Entity\DetailsDelivery;
use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\InputsDetails;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DetailsDeliveryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $inputs = $manager->getRepository(Inputs::class)->findAll();

        foreach ($inputs as $input) {
            $maxFor = rand(1, 3);
            for ($i = 0; $i < $maxFor; $i++) {
                $details = new InputsDetails();
                $details->setEggInput($input);
                $totalEggs = rand(1, 2) * 4800;
                $chicksNumber = $totalEggs * 0.9;
                $details->setChickNumber($chicksNumber);
                $chicksRecipients = $manager->getRepository(ChicksRecipient::class)->findAll();
                $count = count($chicksRecipients) - 1;
                $details->setChicksRecipient($chicksRecipients[rand(0, $count)]);
                $manager->persist($details);


                $herds = $manager->getRepository(Herds::class)->herdsEggsOnWarehouse();
                foreach ($herds as $herd) {
                    if ($herd['eggs'] >= $totalEggs) {
                        $herdsArr [] = $herd[0];
                    }
                }
                $herdsCount = count($herdsArr) - 1;
                $deliveries = $manager->getRepository(Delivery::class)->eggsOnWarehouse($herdsArr[rand(0, $herdsCount)]);
                foreach ($deliveries as $eggsDelivery) {
                    $eggsNumber = $eggsDelivery->getEggsOnWarehouse();
                    if ($totalEggs > 0 && $eggsNumber > 0) {
                        $delivery = new DetailsDelivery();
                        $delivery->setEggsDeliveries($eggsDelivery);
                        $delivery->setEggsInputDetails($details);
                        if ($eggsNumber > $totalEggs) {
                            $delivery->setEggsNumber($totalEggs);
                            $eggsDelivery->setEggsOnWarehouse($eggsNumber - $totalEggs);
                            $totalEggs = 0;
                        } else {
                            $delivery->setEggsNumber($eggsNumber);
                            $eggsDelivery->setEggsOnWarehouse(0);
                            $totalEggs = $totalEggs - $eggsNumber;
                        }
                        $manager->persist($eggsDelivery);
                        $manager->persist($delivery);
                    }

                }
                $manager->flush();
            }
        }


    }
}
