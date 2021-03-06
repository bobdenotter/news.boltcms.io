<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Hits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hits[]    findAll()
 * @method Hits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hits::class);
    }

    public function findOneForToday(array $options): ?Hits
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.createdAt LIKE :date')
            ->andWhere('h.remote = :remote')
            ->setParameter('date', date('Y-m-d%'))
            ->setParameter('remote', $options['remote'])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Hits[] Returns an array of Hits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hits
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
