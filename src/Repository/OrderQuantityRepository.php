<?php

namespace App\Repository;

use App\Entity\OrderQuantity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderQuantity|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderQuantity|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderQuantity[]    findAll()
 * @method OrderQuantity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderQuantityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderQuantity::class);
    }

    // /**
    //  * @return OrderQuantity[] Returns an array of OrderQuantity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderQuantity
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
