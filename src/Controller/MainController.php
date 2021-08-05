<?php

namespace App\Controller;

use App\Entity\Sortie;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
//use Symfony\Component\Serializer\SerializerInterface;

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
        PaginatorInterface $paginator,
        SerializerInterface $serializer
    ): Response
    {
        $listeSorties = $sortieRepository->getSorties();

        $sortiesPaginees = $paginator->paginate(
            $listeSorties,
            $request->query->getInt('page', 1),
            10
        );

        if($request->get('ajax')) {
            $data= $serializer->serialize($listeSorties, 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

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
        $sortie = $sortieRepository->findAll();
        //$sortie = $sortieRepository->getSortieBy(1);

        $data= $serializer->serialize($sortie, 'json');
        //$data = $serializer->serialize($sortie, 'json', SerializationContext::create()->setGroups(['Default']));
        //$data = $serializer->serialize($sortie, 'json', ['groups'=>'sortie', 'user', 'etatSortie', 'campus', 'lieu', 'ville']);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
