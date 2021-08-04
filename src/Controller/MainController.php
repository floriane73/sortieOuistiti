<?php

namespace App\Controller;

use App\Entity\Sortie;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/home", name="main_")
 */
class MainController extends AbstractController
{

    /**
     * @Route("", name="index")
     */
    public function index(
        Request $request,
        SortieRepository $sortieRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $listeSorties = $sortieRepository->findAll();
        dump($listeSorties);

        $sortiesPaginees = $paginator->paginate(
            $listeSorties,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('main/index.html.twig', [
            'listeSorties'=>$sortiesPaginees
        ]);
    }

    /**
     * @Route("/sorties", name="sortiesAPI")
     */
    public function getSorties(
        SortieRepository $sortieRepository,
        SerializerInterface $serializer
    ) {
        $sortie = $sortieRepository->getSortieBy(1);
        dd($sortie);
        $data = $serializer->serialize($sortie, 'json', ['groups'=>'sortie', 'user', 'etatSortie', 'campus', 'lieu', 'ville']);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
