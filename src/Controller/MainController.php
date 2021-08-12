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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route({"/home", "/"}, name="main_")
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
        if($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }
        $userId = $this->getUser()->getId();
        $filtres = new FiltresData();
        $filtres->page = $request->get('page', 1);
        $form = $this->createForm(FiltresType::class, $filtres);

        $form->handleRequest($request);

        $listeSorties = $sortieRepository->getSortiesByFilters($filtres, $userId);

        if($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('main/_sorties.html.twig', ['listeSorties'=>$listeSorties]),
                'sorting' => $this->renderView('main/_sorting.html.twig', ['listeSorties'=>$listeSorties]),
                'pagination' => $this->renderView('main/_pagination.html.twig', ['listeSorties'=>$listeSorties]),
                //'pages' => ceil($listeSorties->getTotalItemCount() / $listeSorties->getItemNumberPerPage())
            ]);
        } else {
            return $this->render('main/index.html.twig',[
                'listeSorties'=>$listeSorties,
                'formFiltres'=>$form->createView()
            ]);
        }
    }

    /*
     @Route("/sorties", name="sortiesAPI")
     */
    /*public function getSorties(
        SortieRepository $sortieRepository,
        SerializerInterface $serializer
    ) {
        $sorties = $sortieRepository->getSortiesByFilters(null);

        $sorties = $sorties->getIterator();

        $data= $serializer->serialize($sorties, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }*/
}
