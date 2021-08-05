<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

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

    public function getSorties() {
        $queryBuilder = $this->createQueryBuilder('sortie');
        $queryBuilder->innerJoin('sortie.campus', 'camp', Join::WITH, 'camp.id = sortie.campus')->addSelect('camp');
        $queryBuilder->innerJoin('sortie.participantOrganisateur', 'orga', Join::WITH, 'orga.id = sortie.participantOrganisateur')->addSelect('orga');
        $queryBuilder->innerJoin('sortie.etatSortie', 'etat', Join::WITH, 'etat.id = sortie.etatSortie')->addSelect('etat');
        $queryBuilder->innerJoin('sortie.lieu', 'lieu', Join::WITH, 'lieu.id = sortie.lieu')->addSelect('lieu');
        $queryBuilder->innerJoin('lieu.ville', 'ville', Join::WITH, 'ville.id = lieu.ville')->addSelect('ville');

        $queryBuilder->addOrderBy('sortie.dateHeureDebut', 'ASC');

        return $queryBuilder->getQuery();
    }

    public function getSortieBy($id) {
        $queryBuilder = $this->createQueryBuilder('sortie');
        $queryBuilder->innerJoin('sortie.campus', 'camp', Join::WITH, 'camp.id = sortie.campus')->addSelect('camp');
        $queryBuilder->innerJoin('sortie.participantOrganisateur', 'orga', Join::WITH, 'orga.id = sortie.participantOrganisateur')->addSelect('orga');
        $queryBuilder->innerJoin('sortie.etatSortie', 'etat', Join::WITH, 'etat.id = sortie.etatSortie')->addSelect('etat');
        $queryBuilder->innerJoin('sortie.lieu', 'lieu', Join::WITH, 'lieu.id = sortie.lieu')->addSelect('lieu');
        $queryBuilder->innerJoin('lieu.ville', 'ville', Join::WITH, 'ville.id = lieu.ville')->addSelect('ville');

        $queryBuilder->where('sortie.id = :id');
        $queryBuilder->setParameter('id', $id);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    public function getSortiesCampus($campus){


    }

}
