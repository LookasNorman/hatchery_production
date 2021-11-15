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

    public function inputsIndex()
    {
        return $this->createQueryBuilder('i')
            ->select('i.id', 'i.name', 'i.inputDate', 'SUM(if.chickNumber) as chickNumber')
            ->leftJoin('i.inputsFarms', 'if')
            ->orderBy('i.inputDate', 'desc')
            ->groupBy('i')
            ->getQuery()
            ->getResult()
            ;
    }

    public function inputsInProduction($date)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.inputDate > :date')
            ->setParameters(['date' => $date])
            ->orderBy('i.inputDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function lightingInputs()
    {
        return $this->createQueryBuilder('i')
            ->addSelect('SUM(ifd.eggsNumber) as eggs')
            ->join('i.inputsFarms', 'if')
            ->join('if.inputsFarmDeliveries', 'ifd')
            ->andWhere('ifd.lighting is null')
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }

    public function inputsNoLighting()
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.inputDeliveries', 'id')
            ->andWhere('id.lighting is empty')
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }

    public function inputsNoTransfer()
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.transfers is empty')
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }

    public function transferInputs()
    {
        return $this->createQueryBuilder('i')
            ->addSelect('SUM(ifd.eggsNumber) as eggs', 'SUM(l.wasteLighting) as waste')
            ->join('i.inputsFarms', 'if')
            ->join('if.inputsFarmDeliveries', 'ifd')
            ->leftJoin('ifd.lighting', 'l')
            ->andWhere('ifd.transfers is null')
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }

    public function inputsNoSelection()
    {
        return $this->createQueryBuilder('i')
            ->join('i.inputDeliveries', 'id')
            ->andWhere('id.selection is empty')
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inputs::class);
    }
}
