<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\FiltresType;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\SortieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
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
        $defaultData = ['message' => 'Votre recherche '];
        $form = $this->createFormBuilder($defaultData)
            ->add('campus', EntityType::class,[
                'class'=>Campus::class,
                'choice_label' => 'nom',
                'required'=>false
            ] )
            ->add('nom', TextType::class,[
                'label' => 'Le nom de la sortie contient',
                'required'=>false
            ])

            ->add('dateDebut', DateType::class,[
                'html5'=>true,
                'required'=>false,
                'widget'=>'single_text'
            ])
            ->add('dateFin', DateType::class,[
                'html5'=>true,
                'required'=>false,
                'widget'=>'single_text'
            ])
            ->add('organisateur', CheckboxType::class,[
                'label'=> 'Sorties dont je suis l\'organisateur/trice',
                'required'=>false
            ])
            ->add('inscrit', CheckboxType::class,[
                'label'=> 'Sorties auxquelles je suis inscrit/e',
                'required'=>false
            ])
            ->add('pasInscrit', CheckboxType::class,[
                'label'=> 'Sorties auxquelles je ne suis pas inscrit/e',
                'required'=>false
            ])
            ->add('passee', CheckboxType::class,[
                'label'=> 'Sorties passées',
                'required'=>false
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Rechercher'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

        }


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
        return $this->render('main/index.html.twig',[
            'listeSorties'=>$sortiesPaginees,
            'filtresForm'=>$form->createView()
        ]);
    }

    /**
     * @Route("/sorties", name="sortiesAPI")
     */
    public function getSorties(
        SortieRepository $sortieRepository,
        SerializerInterface $serializer
    ) {
        $sortie = $sortieRepository->getSortiesByFilters(null, null, 36, null);

        $data= $serializer->serialize($sortie, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
