<?php

namespace App\Controller;

use App\Entity\Sortie;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

        $data= $serializer->serialize($sortie, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
