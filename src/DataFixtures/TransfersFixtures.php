<?php

namespace App\DataFixtures;

use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Transfers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TransfersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $inputs = $manager->getRepository(Inputs::class)->findAll();

        foreach ($inputs as $input) {
            $now = new \DateTime();
            $TransferDate = $input->getInputDate()->add(new \DateInterval('P18D'));
            if($now >= $TransferDate){
                $details = $manager->getRepository(InputsDetails::class)->findBy(['eggInput' => $input]);
                foreach ($details as $detail){
                    $transfer = new Transfers();
                    $transfer->setTransferDate($TransferDate);
                    $wasteEggs = $detail->getChickNumber() / 0.9 * (rand(2, 7) / 100);
                    $transfer->setWasteEggs($wasteEggs);
                    $transfer->setEggsInputsDetail($detail);
                    $manager->persist($transfer);
                }
            }
        }
        $manager->flush();
    }
}
