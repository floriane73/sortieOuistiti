<?php

namespace App\Controller;

use App\Entity\Sortie;
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
     * @Route("/ajout", name="ajout")
     */
    public function ajout(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();

        $sortie->

        $wishForm = $this->createForm(SortieType::class, $sortie);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            $msg = 'Sortie ' . $sortie->getNom() . ' added successfully !';
            $this->addFlash('success', $msg);

            return $this->redirectToRoute('wish_details', ['id' => $wish->getId()]);

        }




        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
