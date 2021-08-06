<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    const SQL2 = "SELECT DISTINCT user.id, user.name, sortie.name
    FROM user
    JOIN sortie as sortie ON user.sortie_id = sortie.id

    WHERE sortie.id IN (
            SELECT sortie2.id FROM sortie sortie2
            JOIN user user2 ON user2.sortie_id=sortie2.id AND user2.id=5
    ) AND user.id != 5
    ORDER BY sortie.date DESC
    LIMIT 10";

}
