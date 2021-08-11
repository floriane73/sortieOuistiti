<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationListeType;
use App\Form\UserType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setAdministrateur(false);
        $user->setActif(true);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('succes', "L'utilisateur a été créé");
            return $this->redirectToRoute('main_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/registerListe", name="app_registerListe")
     */
    public function registerListe(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        $userForm = $this->createForm(RegistrationListeType::class);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            //Récupératon de la data
            //$user = $userForm->getData();

            $file = $userForm['fichierCsv']->getData();


            $data = file($file);

            $campusAll = $entityManager->getRepository(Campus::class)->findAll();

            $count = 0;
            foreach($data as $u){

                $user = new User();
                $campus = new Campus();
                $arrKeywords = explode(",", $u);
                foreach($arrKeywords as $index => $word){
                    $arrKeywords[$index] = trim($word);
                }
                $user->setEmail($arrKeywords[0]);
                $user->setPseudo($arrKeywords[1]);
                $user->setPrenom($arrKeywords[2]);
                $user->setNom($arrKeywords[3]);
                $user->setTelephone($arrKeywords[4]);
                $user->setCampus($campusAll[$arrKeywords[5]-1]);
                $user->setRoles(['ROLE_USER']);
                $user->setAdministrateur(false);
                $user->setActif(true);

                $encoded = $encoder->encodePassword($user, "P@ssw0rd");
                $user->setPassword($encoded);

                $entityManager->persist($user);
                $count++;
            }
            $entityManager->flush();


            $this->addFlash('succes', "Le fichier a été traité. Il y a ". $count ." utilisateur(s) ajouté(s)");
            return $this->redirectToRoute('main_index');
        }

        return $this->render('registration/registerListe.html.twig', [
            'userForm' => $userForm->createView()]);
    }
}
