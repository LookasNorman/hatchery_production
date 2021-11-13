<?php

namespace App\Repository;

use App\Entity\ChicksRecipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChicksRecipient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChicksRecipient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChicksRecipient[]    findAll()
 * @method ChicksRecipient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChicksRecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChicksRecipient::class);
    }

//    public function inputsDelivery($recipient)
//    {
//        return $this->createQueryBuilder('cr')
//            ->addSelect('if', 'ifd')
//            ->join('cr.inputsFarms', 'if')
//            ->join('if.inputsFarmDeliveries', 'ifd')
//            ->where('cr.id = :recipient')
//            ->setParameter('recipient', $recipient)
//            ->getQuery()
//            ->getOneOrNullResult()
//            ;
//    }

    public function chickRecipientsWithPlan()
    {
        return $this->createQueryBuilder('cr')
            ->addSelect('MAX(p.deliveryDate) as deliveryDate')
            ->leftJoin('cr.planDeliveryChicks', 'p')
            ->orderBy('cr.name', 'ASC')
            ->groupBy('cr')
            ->getQuery()
            ->getResult()
            ;
    }

    public function chickRecipientsIntegrationWithPlan($integration)
    {
        return $this->createQueryBuilder('cr')
            ->addSelect('MAX(p.deliveryDate) as deliveryDate')
            ->leftJoin('cr.planDeliveryChicks', 'p')
            ->leftJoin('cr.customer', 'c')
            ->andWhere('c.chickIntegration = :integration')
            ->setParameters(['integration' => $integration])
            ->orderBy('cr.name', 'ASC')
            ->groupBy('cr')
            ->getQuery()
            ->getResult()
            ;
    }

    public function chickRecipientsForCustomerWithPlan($customer)
    {
        return $this->createQueryBuilder('cr')
            ->addSelect('MAX(p.deliveryDate) as deliveryDate')
            ->leftJoin('cr.planDeliveryChicks', 'p')
            ->andWhere('cr.customer = :customer')
            ->setParameters(['customer' => $customer])
            ->orderBy('cr.name', 'ASC')
            ->groupBy('cr')
            ->getQuery()
            ->getResult()
            ;
    }
}
