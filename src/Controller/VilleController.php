<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville", name="ville")
     */
    public function liste(PaginatorInterface $paginator,VilleRepository $villeRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $villes =$villeRepository->findAll();

        $listeVilles = $paginator->paginate($villes, $request->get("page", 1), 10);

        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);
        //dd($villeForm['codePostal']->getData());
        if($villeForm->isSubmitted() && $villeForm->isValid()){

            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'La ville '.$villeForm['nom']->getData().' a bien été ajouté !');
            return  $this->redirectToRoute('ville');
        }

        return $this->render('ville/liste.html.twig', [
            'listeVilles' => $listeVilles,
            'villeForm' => $villeForm->createView()
        ]);
    }
}
