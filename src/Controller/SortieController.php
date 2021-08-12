<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\EtatSortie;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\EtatSortieRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
        #        -- états --
        # 1 - ouverte ( today < cloture )
        # 2 - fermee ( cloture <= today < début )
        # 3 - encours ( début <= today < début+durée )
        # 4 - passee ( début+durée <= today )
        # 5 - annulee
    /**
     * @Route("/", name="blank")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('sortie_ajout');
    }

    /**
     * @Route ("/details/{id}", name="details")
     */
    public function details(
        $id,
        SortieRepository $sortieRepository, EntityManagerInterface $entityManager
    )
    {
        $sortieAffichee = $sortieRepository->getSortieById($id);
        $idEtat = $sortieAffichee->getEtatSortie()->getId();
        $dateLimite = $sortieAffichee->getDateLimiteInscription();
        $dateDebut = $sortieAffichee->getDateHeureDebut();
        $dateFin = date_add($dateDebut, new \DateInterval('P1D'));
        $etatFerme = $entityManager->find(EtatSortie::class, 2);
        $etatEnCours = $entityManager->find(EtatSortie::class, 3);
        $etatPasse = $entityManager->find(EtatSortie::class, 4);

        $today = new \DateTime();

        switch ($idEtat) {
            case 1:
                if ($today > $dateLimite) {
                    $sortieAffichee->setEtatSortie($etatFerme);
                }
                $entityManager->persist($sortieAffichee);
                $entityManager->flush();
                break;
            case 2:
                if ($today > $dateDebut) {
                    $sortieAffichee->setEtatSortie($etatEnCours);
                }
                $entityManager->persist($sortieAffichee);
                $entityManager->flush();
                break;
            case 3:
                if ($today > $dateFin) {
                    $sortieAffichee->setEtatSortie($etatPasse);
                }
                $entityManager->persist($sortieAffichee);
                $entityManager->flush();
        }

        return $this->render('sortie/details.html.twig', [
            "sortieAffichee" => $sortieAffichee
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajout(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $connectedUser = $this->getUser();

        $etatSortie = $entityManager->find(EtatSortie::class, 1);
        $campusUser = $connectedUser->getCampus();

        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortie->setEtatSortie($etatSortie);
        $sortie->setParticipantOrganisateur($connectedUser);
        $sortie->setCampus($campusUser);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $entityManager->persist($sortie);
            $entityManager->flush();

            $msg = 'Sortie ' . $sortie->getNom() . ' ajoutée !';
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/ajout.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }


    /**
     * @Route ("/modifier/{id}", name="modifier")
     */
    public function modifier(
        $id,
        Request $request,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager
    )
    {
        $sortie = $sortieRepository->getSortieById($id);
        $user = $this->getUser();

        if ($sortie->getParticipantOrganisateur()->getId() !== $user->getId()) {
            return $this->redirectToRoute('main_index');
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie = $sortieForm->getData();

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', "Les modifications ont été correctement effectuées !");
            return $this->redirectToRoute('sortie_modifier', ['id' => $id]);
        }

        return $this->render('sortie/modifier.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }


    /**
     * @Route("/sedesister/{id}", name="details_sedesister")
     */
    public function seDesister(int $id, EntityManagerInterface $entityManager)
    {
        //Récupération de l'utilisateur & sortie
        $user = $this->getUser();
        $sortie = $entityManager->getRepository(Sortie::class)->getSortieById($id);

        $data = $sortie->removeParticipantsInscrit($user);
        $entityManager->persist($data);
        $entityManager->flush();

        $this->addFlash('success', "Vous avez bien été désinscrit !");
        return $this->redirectToRoute('sortie_details', array('id' => $id));
    }

    /**
     * @Route("/inscription/{id}", name="details_inscription")
     */
    public function inscription(int $id, EntityManagerInterface $entityManager)
    {
        //Récupération de l'utilisateur & sortie
        $sortie = $entityManager->getRepository(Sortie::class)->getSortieById($id);
        //dd($sortie);
        $user = $this->getUser();

        //Extraction des données pour vérification avant inscription
        $infosSortie = $entityManager->getRepository(Sortie::class)->getSortieById($id);
        $NbInscriptionsMax = $infosSortie->getNbInscriptionsMax();
        $nbParticipantInscrit = count($infosSortie->getParticipantsInscrits());
        $etatSortie = $infosSortie->getEtatSortie()->getId();

        $dateDebutSortie = $infosSortie->getdateHeureDebut();
        $dateLimiteInscription = $infosSortie->getDateLimiteInscription();
        //$dateLimiteInscription= $dateLimiteInscription->format('d-m-Y');
        if ($dateLimiteInscription == null) {
            $dateLimiteInscription = $dateDebutSortie;
        }

        $dateNow = new \DateTime(date("d-m-Y"));
        $interval = $dateNow->diff($dateLimiteInscription);
        $interval = $interval->format('%R%a');
        if ($NbInscriptionsMax == null) {
            $NbInscriptionsMax = $nbParticipantInscrit + 1;
        }


        if ($nbParticipantInscrit < $NbInscriptionsMax && $etatSortie == 1 && $interval > 0) {
            $nbPlaceDispo = $NbInscriptionsMax - $nbParticipantInscrit;
            //Ajout de l'utilisateur à la sortie
            $data = $sortie->addParticipantsInscrit($user);
            $entityManager->persist($data);
            $entityManager->flush();

            $this->addFlash('success', "Vous participerez à cette sortie !");
            return $this->redirectToRoute('sortie_details', array('id' => $id));
        }

        $message = "";
        if ($nbParticipantInscrit == $NbInscriptionsMax) {
            $message = " Le nombre de participants est atteint...";
        }
        if ($etatSortie != 1) {
            $message = " Cette sortie n'est plus ouverte à l'inscription !";
        }
        if ($interval < 0) {
            $message = " Date d'inscription dépassée !";
        }
        $this->addFlash('error', "Désolé , vous ne pouvez pas participer !" . $message);
        return $this->redirectToRoute('sortie_details', array('id' => $id));
    }

    /**
     * @Route("/supprimer/{id}", name="details_supprimer")
     */
    public function supprimer(int $id, EntityManagerInterface $entityManager)
    {
        $sortie = $entityManager->getRepository(Sortie::class)->getSortieById($id);
        $entityManager->persist($sortie);
        $entityManager->remove($sortie);
        $entityManager->flush();

        $this->addFlash('success', "La sortie a bien été effacé !");
        return $this->redirectToRoute('main_index');
    }

    /**
     * @Route("/annuler/{id}", name="details_annuler")
     */
    public function annuler(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $sortie = $entityManager->getRepository(Sortie::class)->getSortieById($id);
        //Vérification que la sortie est annulable
        if ($sortie->getEtatSortie()->getLibelle() != 'Ouverte') {
            $this->addFlash('error', " Il est impossible de (re)annuler cette sortie!");
            return $this->redirectToRoute('main_index');
        }


        $sortieForm = $this->createForm(AnnulerSortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $etatSortie = $entityManager->find(EtatSortie::class, 4);
            $sortie->setEtatSortie($etatSortie);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', "La sortie a bien été annulée !");
            return $this->redirectToRoute('main_index');
        }

        return $this->render('sortie/annuler.html.twig', [
            "sortieForm" => $sortieForm->createView(),
            "sortie" => $sortie
        ]);


    }

}
