<?php

namespace App\Repository;

use App\Data\FiltresData;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Sortie::class);
        $this->paginator = $paginator;
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

    public function updateAllEtats() {
        $queryBuilder = $this->createQueryBuilder('sortie');
    }

    public function getSortiesByFilters(FiltresData $filtres, $userId)
    {
        $queryBuilder = $this->createQueryBuilder('sortie')
            ->select('sortie', 'camp', 'orga', 'inscrits', 'etat', 'lieu', 'ville')
            ->innerJoin('sortie.campus', 'camp', Join::WITH, 'camp = sortie.campus')
            ->innerJoin('sortie.participantOrganisateur', 'orga', Join::WITH, 'orga = sortie.participantOrganisateur')
            ->leftJoin('sortie.participantsInscrits', 'inscrits')
            ->innerJoin('sortie.etatSortie', 'etat', Join::WITH, 'etat = sortie.etatSortie')
            ->innerJoin('sortie.lieu', 'lieu', Join::WITH, 'lieu = sortie.lieu')
            ->innerJoin('lieu.ville', 'ville', Join::WITH, 'ville = lieu.ville')
            ->andWhere("CURRENT_DATE()<= DATE_ADD(sortie.dateHeureDebut,30, 'day')");

        if (!empty($filtres->q)) {
            $queryBuilder->andWhere('sortie.nom LIKE :words')
                ->setParameter('words', '%' . $filtres->q . '%');
        }
        if (!empty($filtres->isOrganisateur)) {
            $queryBuilder->andWhere('orga.id = :organisateur')
                ->setParameter('organisateur', $userId);
        }
        if (!empty($filtres->isParticipant)) {
            $queryBuilder->leftJoin('sortie.participantsInscrits', 'usr')
                ->andWhere('usr.id = :participant')
                ->setParameter('participant', $userId);
        }
        if (!empty($filtres->isNotParticipant)) {
            $queryBuilder->leftJoin('sortie.participantsInscrits', 'usr2')
            ->andWhere('usr2.id != :nonParticipant')
                ->setParameter('nonParticipant', $userId);
        }
        if (!empty($filtres->campus)) {
            $queryBuilder->andWhere('camp = :campus')
                ->setParameter('campus', $filtres->campus);
        }
        if (!empty($filtres->dateMin)) {
            $queryBuilder->andWhere(':dateMin < sortie.dateHeureDebut')
                ->setParameter('dateMin', $filtres->dateMin);
        }
        if (!empty($filtres->dateMax)) {
            $queryBuilder->andWhere(':dateMax > sortie.dateHeureDebut')
                ->setParameter('dateMax', $filtres->dateMax);
        }
        if (!empty($filtres->isSortiePassee)) {
            $queryBuilder->andWhere('etat.id = :etat')
                ->setParameter('etat', 3);
        }
        $queryBuilder->addOrderBy('sortie.dateLimiteInscription', 'ASC');

        $pageN = 0;

        $queryBuilder->setMaxResults(10)->setFirstResult($pageN*10);

        $results = $this->paginator->paginate(
            $queryBuilder,
            $filtres->page,
            10
        );

        return $results;
    }
}
