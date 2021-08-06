<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function getSorties()
    {
        $queryBuilder = $this->createQueryBuilder('sortie');
        $queryBuilder->innerJoin('sortie.campus', 'camp', Join::WITH, 'camp = sortie.campus')->addSelect('camp');
        $queryBuilder->innerJoin('sortie.participantOrganisateur', 'orga', Join::WITH, 'orga = sortie.participantOrganisateur')->addSelect('orga');
        $queryBuilder->leftJoin('sortie.participantsInscrits', 'inscrits')->addSelect('inscrits');
        $queryBuilder->innerJoin('sortie.etatSortie', 'etat', Join::WITH, 'etat = sortie.etatSortie')->addSelect('etat');
        $queryBuilder->innerJoin('sortie.lieu', 'lieu', Join::WITH, 'lieu = sortie.lieu')->addSelect('lieu');
        $queryBuilder->innerJoin('lieu.ville', 'ville', Join::WITH, 'ville = lieu.ville')->addSelect('ville');

        $queryBuilder->addOrderBy('sortie.dateHeureDebut', 'ASC');


        return $queryBuilder->getQuery()->getResult();
    }

    public function getSortieById($id)
    {
        $queryBuilder = $this->createQueryBuilder('sortie');
        $queryBuilder->innerJoin('sortie.campus', 'camp', Join::WITH, 'camp = sortie.campus')->addSelect('camp');
        $queryBuilder->innerJoin('sortie.participantOrganisateur', 'orga', Join::WITH, 'orga = sortie.participantOrganisateur')->addSelect('orga');
        $queryBuilder->leftJoin('sortie.participantsInscrits', 'inscrits')->addSelect('inscrits');
        $queryBuilder->innerJoin('sortie.etatSortie', 'etat', Join::WITH, 'etat = sortie.etatSortie')->addSelect('etat');
        $queryBuilder->innerJoin('sortie.lieu', 'lieu', Join::WITH, 'lieu = sortie.lieu')->addSelect('lieu');
        $queryBuilder->innerJoin('lieu.ville', 'ville', Join::WITH, 'ville = lieu.ville')->addSelect('ville');

        $queryBuilder->where('sortie.id = :id');
        $queryBuilder->setParameter('id', $id);


        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getSortiesByFilters($keywords = null,
                                        $idOrganisateur = null,
                                        $idParticipant = null,
                                        $idNonParticipant=null,
                                        $idCampus = null,
                                        $dateMin = null,
                                        $dateMax = null,
                                        $idEtat = null,
                                        $pageN = 0)
    {
        $queryBuilder = $this->createQueryBuilder('sortie')
            ->select('sortie')
            ->innerJoin('sortie.campus', 'camp', Join::WITH, 'camp = sortie.campus')
            ->innerJoin('sortie.participantOrganisateur', 'orga', Join::WITH, 'orga = sortie.participantOrganisateur')
            ->leftJoin('sortie.participantsInscrits', 'inscrits')
            ->innerJoin('sortie.etatSortie', 'etat', Join::WITH, 'etat = sortie.etatSortie')
            ->innerJoin('sortie.lieu', 'lieu', Join::WITH, 'lieu = sortie.lieu')
            ->innerJoin('lieu.ville', 'ville', Join::WITH, 'ville = lieu.ville');

        if ($keywords !== null) {
            $queryBuilder->andWhere('sortie.nom LIKE :words')
                ->setParameter('words', '%' . $keywords . '%');
        }
        if ($idOrganisateur !== null) {
            $queryBuilder->andWhere('orga.id = :organisateur')
                ->setParameter('organisateur', $idOrganisateur);
        }
        if ($idParticipant !== null) {
            $queryBuilder->leftJoin('sortie.participantsInscrits', 'usr')
                ->andWhere('usr.id = :participant')
                ->setParameter('participant', $idParticipant);
        }
        if ($idNonParticipant !== null) {
            $queryBuilder->leftJoin('sortie.participantsInscrits', 'usr2')
            ->andWhere('usr2.id != :nonParticipant')
                ->setParameter('nonParticipant', $idNonParticipant);
        }
        if ($idCampus !== null) {
            $queryBuilder->andWhere('camp.id = :campus')
                ->setParameter('campus', $idCampus);
        }
        if ($dateMin !== null) {
            $queryBuilder->andWhere('DATE_DIFF(:dateMin, CURRENT_DATE()) > 0')
                ->setParameter('dateMin', $dateMin);
        }
        if ($dateMax != null) {
            $queryBuilder->andWhere('DATE_DIFF(:dateMax, CURRENT_DATE()) < 0')
                ->setParameter('dateMax', $dateMax);
        }
        if ($idEtat != null) {
            $queryBuilder->andWhere('etat.id = :etat')
                ->setParameter('etat', $idEtat);
        }

        $queryBuilder->setMaxResults(10)
            ->setFirstResult($pageN*10);

        $paginator = new Paginator();

        return $queryBuilder->getQuery()->getResult();
    }
}
