<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\EtatSortie;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{

    /**
     * @Route ("/details/{id}", name="details")
     */
    public function details(
        $id,
        SortieRepository $sortieRepository
    )
    {
        $connectedUser= $this->getUser();

        $sortieAffichee = $sortieRepository->getSortieBy($id);

        //dd($sortieAffichee);

        return $this->render('sortie/details.html.twig', [
            "sortieAffichee"=>$sortieAffichee
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajout(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $connectedUser = $this->getUser();

        $etatSortie = $entityManager->find(EtatSortie::class, 1);
        $campusUser = $connectedUser->getCampus();

        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortie->setEtatSortie($etatSortie);
        $sortie->setParticipantOrganisateur($connectedUser);
        $sortie->setCampus($campusUser);

        dump($sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            $msg = 'Sortie ' . $sortie->getNom() . ' ajoutée !';
            $this->addFlash('success', $msg);

            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }


        return $this->render('sortie/ajout.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }
}
