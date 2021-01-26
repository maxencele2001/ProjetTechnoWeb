<?php

namespace App\Repository;

use App\Entity\Restaurant;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function getByType(Type $type)
    {
        return $this->createQueryBuilder('r')
        ->where('r.type = :type')
        ->setParameter('type', $type)
        ->getQuery()
        ->getResult();
    }

    public function getByName(Restaurant $name)
    {
        return $this->createQueryBuilder('r')
        ->where('r.name = :name')
        ->setParameter('name', $name)
        ->getQuery()
        ->getResult();
    }
    
    public function getByIdUser(User $user)
    {
        return $this->createQueryBuilder('r')
        ->where('r.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
    }

    public function findTop(int $number)
  {

    $qb = $this->createQueryBuilder('a')
        ->orderBy('a.id', 'DESC')
        ->setMaxResults($number);
    
    return $qb->getQuery()->getResult();
    
  }

  

  
    // /**
    //  * @return Restaurant[] Returns an array of Restaurant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Restaurant
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
