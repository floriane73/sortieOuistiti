<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\EtatSortie;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $generator;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->generator = Factory::Create('fr_FR');

        $this->addVille();
        $this->addLieu();
        $this->addEtat();
        $this->addCampus();
        $this->addParticipant();
        $this->addSortie();
    }

    private function addVille(int $number = 20)
    {
        for($i=0 ; $i<=$number; $i++){
            //parametrage
            $codePostal = rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9);

            $ville = new Ville();
            $ville->setNom($this->generator->city)
                  ->setCodePostal($codePostal);

            $this->manager->persist($ville);
        }
        $this->manager->flush();
    }


    private function addLieu(int $number=20)
    {
        $ville = $this->manager->getRepository(Ville::class)->findAll();
        $lieux = ["Le Cap", "La Courtille", "Chez MoMo", "La zone", "Pont Caf"];

        for($i=0 ; $i<=$number; $i++){
            //Parametrage
            $numeroEtRue =  (rand(0,4) != 1) ? $this->generator->streetAddress : null;
            $latitude =  (rand(0,4) != 1) ? $this->generator->latitude : null;
            $longitude = ($latitude != null) ? $this->generator->longitude : null;

            $lieu = new Lieu();
            $lieu->setNom($lieux[rand(0, count($lieux)-1)])
                 ->setNumeroEtRue($numeroEtRue)
                 ->setLatitude($latitude)
                 ->setLongitude($longitude)
                 ->setVille($this->generator->randomElement($ville));
            $this->manager->persist($lieu);
        }

        $this->manager->flush();
    }


    private function addEtat()
    {
        $etats = ["Ouverte", "Fermée", "En cours", "Passée", "Annulée"];

        for($i=0; $i<count($etats); $i++){
            $etat = new EtatSortie();
            $etat->setLibelle($etats[$i]);

            $this->manager->persist($etat);
        }
        $this->manager->flush();
    }

    private function addCampus()
    {
        $listeCampus = ["Eni Informatique", "Digital School", "ESG Data", "Digital College", "Ecole Ingenieur"];

        for($i=0; $i<count($listeCampus); $i++){
            $campus = new Campus();
            $campus->setNom($listeCampus[$i]);

            $this->manager->persist($campus);
        }
        $this->manager->flush();
    }

    private function addParticipant()
    {
        $number=100;
        $campus = $this->manager->getRepository(Campus::class)->findAll();
        //0.8 est le pourcentage de participants actif
        $totalParticipantInactif = $number - ceil(($number * 0.8));
//        $etat = false;

        for($i=0; $i<$number; $i++){

            //Paramètrage
            $etat = ($i >= $totalParticipantInactif ) ? true : false;
            $numTel = (rand(0,4) !=0) ? "0".rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9) : null;

            $participant = new User();
            $participant->setNom($this->generator->lastName)
                        ->setPrenom($this->generator->firstName)
                        ->setPseudo($this->generator->userName)
                        ->setTelephone($numTel)
                        ->setEmail($this->generator->email)
                        ->setPassword($this->generator->password)
                        ->setAdministrateur(false)
                        ->setActif($etat)
                        ->setCampus($this->generator->randomElement($campus))
                        ->setRoles(["ROLE_USER"]);
            $this->manager->persist($participant);
        }
        $this->manager->flush();
    }

    private function addSortie()
    {
        $number=20;

        $listeLieux = ["Terrain de foot", "Restaurant", "Patinoire", "Centre commercial", "Palais des sports", "Parking école", "Escalade", "Laser Games", "Brasserie", "Forum", "Salon", "Musée", "Concert"];
        $lieu = $this->manager->getRepository(Lieu::class)->findAll();
        $participants = $this->manager->getRepository(User::class)->findAll();

        //$etats = $this->manager->getRepository(EtatSortie::class)->findAll();
        $etatOuvert = $this->manager->find(EtatSortie::class,1);
        $etatFerme = $this->manager->find(EtatSortie::class,2);
        $etatEnCours = $this->manager->find(EtatSortie::class,3);
        $etatFini = $this->manager->find(EtatSortie::class,4);

        $campus = $this->manager->getRepository(Campus::class)->findAll();
        $listeDuree = [null,0,10,20,30,60,90,120,180,210,240,270,300,330,360,390,420];



        for($i=0; $i<=$number; $i++){
            //paramètrage
            $duree = $listeDuree[rand(0, count($listeDuree)-1)];
            $dateHeureDebut = $this->generator->dateTimeBetween('-6 months', '+1 years');
            $dateDebutSortie = date_format($dateHeureDebut, "y-m-d");
            if(rand(0,4) != 0){
                //$dateLimiteInscription = date('y-m-d', strtotime($dateDebutSortie. ' - '.rand(2,30).' days'));
                $dateLimiteInscription = $this->generator->dateTimeBetween(date('y-m-d', strtotime($dateDebutSortie. ' - '.rand(1,10).' days')),$dateDebutSortie);
            } else {
                $dateLimiteInscription = null;
            }

            $nbParticipant = rand(5,50);
            if($nbParticipant >10 && $nbParticipant<20){
                $nbParticipant = null;
            }
            $description = (rand(0,9) !=0) ? $this->generator->sentence : null;


            $dateNow=new \DateTime(date("d-m-Y"));
            if($dateLimiteInscription != null) {
                $timestampLimiteInscription = $dateLimiteInscription->getTimestamp();
            } else {
                $timestampLimiteInscription = $dateHeureDebut->getTimestamp();
            }

            $timestampDebutSortie = $dateHeureDebut->getTimestamp();
            if($duree == null){
                $dateFinSortie = $dateHeureDebut;
            } else {
                $dateFinSortie = $dateHeureDebut->add(new \DateInterval('PT'.$duree.'M'));
            }

            $timestampFinSortie = $dateFinSortie->getTimestamp();
            //var_dump("Fin sortie :".$timestampFinSortie);
            $timestampNow = $dateNow->getTimestamp();

            switch ($timestampNow) {
                case ($timestampNow < $timestampDebutSortie && $timestampNow < $timestampLimiteInscription) :
                    $etatSortie = $etatOuvert;
                    break;
                case ($timestampNow < $timestampDebutSortie && $timestampNow >= $timestampLimiteInscription) :
                    $etatSortie = $etatFerme;
                    break;
                case ($timestampNow > $timestampFinSortie) :
                    $etatSortie = $etatFini;
                    break;
                case ($timestampNow > $timestampDebutSortie && $timestampNow < $timestampFinSortie) :
                    $etatSortie = $etatEnCours;
                    break;
            }

            $sortie = new Sortie();
            $sortie->setNom($listeLieux[rand(0,count($listeLieux)-1)])
                   ->setCampus($this->generator->randomElement($campus))
                   ->setDescription($description)
                   ->setDateHeureDebut($dateHeureDebut)
                   ->setDuree($duree)
                   ->setDateLimiteInscription($dateLimiteInscription)
                   ->setEtatSortie($etatSortie)
                   ->setLieu($this->generator->randomElement($lieu))
                   ->setParticipantOrganisateur($this->generator->randomElement($participants))
                   ->setNbInscriptionsMax($nbParticipant);
                for($i=0; $i<=rand(0,$nbParticipant); $i++){
                    $sortie ->addParticipantsInscrit($this->generator->randomElement($participants));
                 }

                $this->manager->persist($sortie);

        }
        $this->manager->flush();
    }


}
