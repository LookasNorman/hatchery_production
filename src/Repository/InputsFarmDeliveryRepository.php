<?php

namespace App\Repository;

use App\Entity\InputsFarmDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InputsFarmDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputsFarmDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputsFarmDelivery[]    findAll()
 * @method InputsFarmDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputsFarmDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputsFarmDelivery::class);
    }

    public function findByExampleField($input, $herd)
    {
        return $this->createQueryBuilder('ifd')
            ->join('ifd.inputsFarm', 'if')
            ->join('if.eggInput', 'ei')
            ->join('ifd.delivery', 'd')
            ->andWhere('ei.id = :input')
            ->andWhere('d.herd = :herd')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?InputsFarmDelivery
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
