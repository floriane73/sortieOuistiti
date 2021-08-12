<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LieuController extends AbstractController
{
    /**
     * @Route("/lieu", name="lieu")
     */
    public function liste(PaginatorInterface $paginator, LieuRepository $lieuRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $lieux =$lieuRepository->findAll();

        $listeLieux = $paginator->paginate($lieux, $request->get("page", 1), 10);

        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu '.$lieuForm['nom']->getData().' a bien été ajouté !');
            return  $this->redirectToRoute('lieu');
        }

        return $this->render('lieu/liste.html.twig', [
            'listeLieux' => $listeLieux,
            'lieuForm' => $lieuForm->createView()
        ]);
    }


}
