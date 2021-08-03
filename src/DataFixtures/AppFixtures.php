<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\EtatSortie;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
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
        $etats = ["Ouverte", "Activité en cours", "Passée", "Annulée"];

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

    private function addParticipant(int $number=100)
    {
        $campus = $this->manager->getRepository(Campus::class)->findAll();
        //0.8 est le pourcentage de participants actif
        $totalParticipantInactif = $number - ceil(($number * 0.8));
//        $etat = false;

        for($i=0; $i<$number; $i++){

            //Paramètrage
            $etat = ($i >= $totalParticipantInactif ) ? true : false;
            $numTel = (rand(0,4) !=0) ? "0".rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9) : null;

            $participant = new Participant();
            $participant->setNom($this->generator->lastName)
                        ->setPrenom($this->generator->firstName)
                        ->setPseudo($this->generator->userName)
                        ->setTelephone($numTel)
                        ->setMail($this->generator->email)
                        ->setMotPasse($this->generator->password)
                        ->setAdministrateur(false)
                        ->setActif($etat)
                        ->setCampus($this->generator->randomElement($campus));
            $this->manager->persist($participant);
        }
        $this->manager->flush();
    }

    private function addSortie(int $number=20)
    {
        $listeLieux = ["Terrain de foot", "Restaurant", "Patinoire", "Centre commercial", "Palais des sports", "Parking école", "Escalade", "Laser Games", "Brasserie", "Forum", "Salon", "Musée", "Concert"];
        $lieu = $this->manager->getRepository(Lieu::class)->findAll();
        $participant = $this->manager->getRepository(Participant::class)->findAll();
        $etats = $this->manager->getRepository(EtatSortie::class)->findAll();
        $campus = $this->manager->getRepository(Campus::class)->findAll();
        $listeDuree = [null,0,10,20,30,60,90,120,180,240,300,330,360];

        for($i=0; $i<=$number; $i++){
            //paramètrage
            $dateHeureDebut = $this->generator->dateTimeBetween('-1 years', '+1 years');
            $dateDebutSortie = date_format($dateHeureDebut, "y-m-d");
            if(rand(0,4) != 0){
                //$dateLimiteInscription = date('y-m-d', strtotime($dateDebutSortie. ' - '.rand(2,30).' days'));
                $dateLimiteInscription = $this->generator->dateTimeBetween(date('y-m-d', strtotime($dateDebutSortie. ' - '.rand(1,10).' days')),$dateDebutSortie);
            } else {
                $dateLimiteInscription = null;
                $dateDebutSortie = null;
            }

            $nbParticipant = rand(5,50);
            if($nbParticipant >10 && $nbParticipant<20){
                $nbParticipant = null;
            }
            $description = (rand(0,9) !=0) ? $this->generator->sentence : null;


            $sortie = new Sortie();
            $sortie->setNom($listeLieux[rand(0,count($listeLieux)-1)])
                   ->setCampus($this->generator->randomElement($campus))
                   ->setDescription($description)
                   ->setDateHeureDebut($dateHeureDebut)
                   ->setDuree($listeDuree[rand(0, count($listeDuree)-1)])
                   ->setDateLimiteInscription($dateLimiteInscription)
                   ->setEtatSortie($this->generator->randomElement($etats))
                   ->setLieu($this->generator->randomElement($lieu))
                   ->setParticipantOrganisateur($this->generator->randomElement($participant))
                   ->setNbInscriptionsMax($nbParticipant);

            $this->manager->persist($sortie);
        }
        $this->manager->flush();
    }


}
