<?php

namespace App\Repository;

use App\Entity\EtatSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EtatSortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatSortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatSortie[]    findAll()
 * @method EtatSortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatSortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatSortie::class);
    }

    // /**
    //  * @return EtatSortie[] Returns an array of EtatSortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EtatSortie
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
