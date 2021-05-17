<?php

namespace App\Repository;

use App\Entity\Inputs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Inputs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inputs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inputs[]    findAll()
 * @method Inputs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inputs::class);
    }

    public function inputsNoLighting()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('eid')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsInputsLightings', 'il')
            ->leftJoin('eid.eggsInputsDetailsEggsDeliveries', 'idd')
            ->where('il.id IS NULL')
            ->orderBy('e.inputDate')
            ->getQuery()
            ->getResult()
            ;
    }

    public function inputsNoTransfer()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('eid')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsInputsTransfers', 'it')
            ->leftJoin('eid.eggsInputsDetailsEggsDeliveries', 'idd')
            ->where('it.id IS NULL')
            ->orderBy('e.inputDate')
            ->getQuery()
            ->getResult()
            ;
    }

    public function inputsNoSelection()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('eid')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsSelections', 'es')
            ->leftJoin('eid.eggsInputsDetailsEggsDeliveries', 'idd')
            ->where('es.id IS NULL')
            ->orderBy('e.inputDate')
            ->getQuery()
            ->getResult()
            ;
    }

    public function inputsLighting()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('SUM(idd.eggsNumber) as eggsNumber')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsInputsLightings', 'il')
            ->leftJoin('eid.eggsInputsDetailsEggsDeliveries', 'idd')
            ->where('il.id IS NULL')
            ->groupBy('e.id')
            ->orderBy('e.inputDate')
            ->addOrderBy('e.name')
            ->getQuery()
            ->getResult()
            ;
    }

    public function inputsTransfers()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('SUM(idd.eggsNumber) as eggsNumber', 'SUM(il.wasteEggs) as wasteEggs')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsInputsTransfers', 'it')
            ->leftJoin('eid.eggsInputsDetailsEggsDeliveries', 'idd')
            ->leftJoin('eid.eggsInputsLightings', 'il')
            ->where('it.id IS NULL')
            ->groupBy('e.id')
            ->orderBy('e.inputDate')
            ->addOrderBy('e.name')
            ->getQuery()
            ->getResult()
            ;
    }

    public function inputsPickings()
    {
        return $this->createQueryBuilder('e')
            ->addSelect('SUM(eid.chickNumber) as chickNumber')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsSelections', 'es')
            ->where('es.id IS NULL')
            ->groupBy('e.id')
            ->orderBy('e.inputDate')
            ->addOrderBy('e.name')
            ->getQuery()
            ->getResult()
            ;
    }

}
