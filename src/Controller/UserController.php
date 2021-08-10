<?php

namespace App\Controller;

use App\Entity\EtatSortie;
use App\Entity\User;
use App\Form\AnnulerSortieType;
use App\Form\UserType;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route ("/user", name="user_")
 */

class   UserController extends AbstractController
{
    /**
     * @Route("/", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',

        ]);
    }

    /**
     * @Route("/modifier", name="modifier_profil")
     */
    public function profil(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        //récuperation de l'utilisateur
        $user = $this->getUser();
        $avatarPath = $user->getAvatarPath();

        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid() ){
            //Hash du mot de passe
            //$user = new User();
            $plainPassword = $userForm['password']->getData();
            $encoded = $encoder->encodePassword($user, $plainPassword);

            //Récupératon de la data
            $user = $userForm->getData();
            $user->setPassword($encoded);


            $file = $userForm['avatarPath']->getData();
            if($file !== null){
                if($file){
                    //renomme aleatoirement l'image
                    $filename = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
                    try {
                        $file->move($this->getParameter('folderAvatar'), $filename);
                    } catch (FileException $e) {
                        // unable to upload the photo, give up
                    }
                    $user->setAvatarPath($filename);
                }
            }else{
                $user->setAvatarPath($avatarPath);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "Les modifications ont été correctement effectuées !");
            return $this->redirectToRoute('user_modifier_profil'); //Modifier la route
        }

        return $this->render('user/modifier.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/afficher/{id}", name="afficher_profil")
     */
    public function afficher(int $id, UserRepository $userRepository)
    {
        $result = $userRepository->find($id);

     return $this->render("user/afficher.html.twig", [
        'profil' => $result
     ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer_profil")
     */
    public function supprimer(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $userCurrent = $entityManager->getRepository(User::class)->find($id);
        if($userCurrent->getAdministrateur()){
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un administrateur');
            return $this->redirectToRoute('user_supprimer_utilisateurs');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash("success", "L'utilisateur ". $id." a bien été effacé !");
        if($request->get('backToDashboard') == 1){
            return $this->redirectToRoute('user_supprimer_utilisateurs');

        }

        return $this->redirectToRoute('main_index');

    }

    /**
     * @Route("/supprimerUtilisateurs", name="supprimer_utilisateurs")
     */
    public function supprimerUtilisateurs(EntityManagerInterface $entityManager, Request $request)
    {
        $allUsers = $entityManager->getRepository(User::class)->findAll();

        return $this->render("/user/supprimerUtilisateurs.html.twig",[
           "allUsers" => $allUsers
        ]);
    }

    /**
     * @Route("/desactiver/{id}", name="desactiver_utilisateur")
     */
    public function desactiver(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, SortieRepository $sortieRepository)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        $userSorties = $sortieRepository->findBy(array('participantOrganisateur' => $id));
        $sortieAnnulee = $entityManager->getRepository(EtatSortie::class)->find(4);

        foreach($userSorties as $sortie){
            $description = $sortie->getDescription();
            $sortie->setDescription("SORTIE ANNULEE - ".$description);
            $sortie->setEtatSortie($sortieAnnulee);
           // $user->setRoles(["ROLE_DESACTIVE"]);
        }

        $user->setActif(false);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_supprimer_utilisateurs');

    }

}
