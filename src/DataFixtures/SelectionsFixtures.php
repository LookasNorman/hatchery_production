<?php

namespace App\DataFixtures;

use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Selections;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SelectionsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $inputs = $manager->getRepository(Inputs::class)->findAll();

        foreach ($inputs as $input) {
            $now = new \DateTime();
            $SelectionsDate = $input->getInputDate()->add(new \DateInterval('P21D'));
            if($now >= $SelectionsDate){
                $details = $manager->getRepository(InputsDetails::class)->findBy(['eggInput' => $input]);
                foreach ($details as $detail){
                    $selection = new Selections();
                    $selection->setSelectionDate($SelectionsDate);
                    $chickNumber = $detail->getChickNumber() * (rand(1, 3) / 100);
                    $cullChick = $detail->getChickNumber() - $chickNumber;
                    $selection->setChickNumber($chickNumber);
                    $selection->setCullChicken($cullChick);
                    $selection->setEggsInputsDetail($detail);
                    $manager->persist($selection);
                }
            }
        }
        $manager->flush();
    }
}
