<?php

namespace App\Repository;

use App\Entity\InputsFarm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InputsFarm|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputsFarm|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputsFarm[]    findAll()
 * @method InputsFarm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputsFarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputsFarm::class);
    }

    public function chickInInput($input)
    {
        return $this->createQueryBuilder('if')
            ->select('SUM(if.chickNumber)')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['input' => $input])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
