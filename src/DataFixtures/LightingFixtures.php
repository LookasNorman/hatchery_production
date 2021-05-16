<?php

namespace App\DataFixtures;

use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Lighting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LightingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $inputs = $manager->getRepository(Inputs::class)->findAll();

        foreach ($inputs as $input) {
            $now = new \DateTime();
            $lightingDate = $input->getInputDate()->add(new \DateInterval('P12D'));
            if($now >= $lightingDate){
                $details = $manager->getRepository(InputsDetails::class)->findBy(['eggInput' => $input]);
                foreach ($details as $detail){
                    $lighting = new Lighting();
                    $lighting->setLightingDate($lightingDate);
                    $wasteEggs = $detail->getChickNumber() / 0.9 * (rand(5, 15) / 100);
                    $lighting->setWasteEggs($wasteEggs);
                    $lighting->setEggsInputsDetail($detail);
                    $manager->persist($lighting);
                }
            }
        }
        $manager->flush();
    }
}
