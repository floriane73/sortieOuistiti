<?php

namespace App\Controller;

use App\Data\FiltresData;
use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\FiltresType;
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
        $userId = $this->getUser()->getId();
        $filtres = new FiltresData();
        $filtres->page = $request->get('page', 1);
        $form = $this->createForm(FiltresType::class, $filtres);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
        }

        $listeSorties = $sortieRepository->getSortiesByFilters($filtres, $userId);

        /*if($request->get('ajax')) {
            $data= $serializer->serialize($listeSorties, 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }*/

        dump($listeSorties);

        return $this->render('main/index.html.twig',[
            'listeSorties'=>$listeSorties,
            'formFiltres'=>$form->createView()
        ]);
    }

    /**
     * @Route("/sorties", name="sortiesAPI")
     */
    public function getSorties(
        SortieRepository $sortieRepository,
        SerializerInterface $serializer
    ) {
        $sorties = $sortieRepository->getSortiesByFilters(null);

        $sorties = $sorties->getIterator();

        $data= $serializer->serialize($sorties, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
