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

    public function inputsInProduction($date)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.inputDate > :date')
            ->setParameters(['date' => $date])
            ->orderBy('i.inputDate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inputs::class);
    }
}
