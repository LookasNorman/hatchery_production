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

}
