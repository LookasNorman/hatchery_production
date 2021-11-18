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
    public function inputsReminder()
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.inputDate between :date and :end')
            ->setParameters(['date' => new \DateTime('-57 days'), 'end' => new \DateTime('-49 days')])
            ->getQuery()
            ->getResult()
            ;
    }

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
