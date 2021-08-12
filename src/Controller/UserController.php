<?php

namespace App\Controller;

use App\Entity\EtatSortie;
use App\Entity\User;
use App\Form\AnnulerSortieType;
use App\Form\UserType;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->redirectToRoute('user_modifier_profil');
    }

    /**
     * @Route("/modifier", name="modifier_profil")
     */
    public function profil(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        //récuperation de l'utilisateur
        $user = $this->getUser();
        $avatarPath = $user->getAvatarPath();

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();

            //Hash du mot de passe
            /*$plainPassword = $userForm['password']->getData();
            dump($plainPassword);
            if ($plainPassword !== null) {
                $encoded = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($encoded);
            } else {
                $user->setPassword($passwordPrev);
            }*/

            //Récupératon de la data
            $file = $userForm['avatarPath']->getData();
            if ($file !== null) {
                if ($file) {
                    //renomme aleatoirement l'image
                    $filename = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
                    try {
                        $file->move($this->getParameter('folderAvatar'), $filename);
                    } catch (FileException $e) {
                        // unable to upload the photo, give up
                    }
                    $user->setAvatarPath($filename);
                }
            } else {
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
     * @Route("/details/{id}", name="afficher_profil")
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
        if ($userCurrent->getAdministrateur()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un administrateur');
            return $this->redirectToRoute('user_supprimer_utilisateurs');
        }
        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash("success", "L'utilisateur ". $id." a bien été effacé !");
        if ($request->get('backToDashboard') == 1) {
            return $this->redirectToRoute('user_supprimer_utilisateurs');

        }
        return $this->redirectToRoute('main_index');
    }

    /**
     * @Route("/supprimerUtilisateurs", name="supprimer_utilisateurs")
     */
    public function supprimerUtilisateurs(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator)
    {
        $allUsers = $entityManager->getRepository(User::class)->findAll();
        $listeUsers = $paginator->paginate($allUsers, $request->get("page", 1), 10);

        return $this->render("/user/supprimerUtilisateurs.html.twig",[
           "allUsers" => $listeUsers
        ]);
    }

    /**
     * @Route("/desactiver/{id}", name="desactiver_utilisateur")
     */
    public function desactiver(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, SortieRepository $sortieRepository)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        $userSortiesInscrit = $user->getSortiesChoisies();
        $userSortiesOrganise = $user->getSortiesOrganisees();
        $sortieAnnulee = $entityManager->getRepository(EtatSortie::class)->find(4);

        foreach($userSortiesOrganise as $sortie){
            $description = $sortie->getDescription();
            $sortie->setDescription("SORTIE ANNULEE - ".$description);
            $sortie->setEtatSortie($sortieAnnulee);
        }

        foreach ($userSortiesInscrit as $sortieInscrit)
        {
            $sortieInscrit->removeParticipantsInscrit($user);
        }

        $user->setActif(false);
        $user->setRoles(["ROLE_DESACTIVE"]);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur '.$id.' a bien été désactivé !');
        return $this->redirectToRoute('user_supprimer_utilisateurs');

    }

}
