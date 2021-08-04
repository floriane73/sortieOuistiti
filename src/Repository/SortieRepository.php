<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function getSortieBy($id) {
        $queryBuilder = $this->createQueryBuilder('sortie');
        $queryBuilder->innerJoin('sortie.campus', 'camp')->addSelect('camp');
        $queryBuilder->innerJoin('sortie.participantOrganisateur', 'orga')->addSelect('orga');
        $queryBuilder->innerJoin('sortie.etatSortie', 'etat')->addSelect('etat');
        $queryBuilder->innerJoin('sortie.lieu', 'lieu')->addSelect('lieu');
        $queryBuilder->innerJoin('lieu.ville', 'ville')->addSelect('ville');

        $queryBuilder->where('sortie.id = :id');
        $queryBuilder->setParameter('id', $id);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    public function getSortiesCampus($campus){


    }

}
