<?php

namespace App\Repository;

use App\Entity\Lighting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lighting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lighting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lighting[]    findAll()
 * @method Lighting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LightingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lighting::class);
    }

    public function herdLightingInInput($herd, $input)
    {
        return $this->createQueryBuilder('l')
            ->select('SUM(l.wasteEggs) as wasteEggs')
            ->addSelect('SUM(l.wasteLighting) as wasteLighting')
            ->addSelect('SUM(l.lightingEggs) as lightingEggs')
            ->addSelect('l.lightingDate')
            ->join('l.inputsFarmDelivery', 'ifd')
            ->join('ifd.delivery', 'd')
            ->join('ifd.inputsFarm', 'if')
            ->andWhere('d.herd = :herd')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['herd' => $herd, 'input' => $input])
            ->groupBy('l.lightingDate')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Lighting
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
