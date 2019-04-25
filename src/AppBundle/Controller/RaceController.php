<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Horse;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends Controller
{
    /**
     * @Route("/create-race", name="create_race")
     */
    public function createRaceAction()
    {

        $raceRepository = $this->getDoctrine()->getRepository(Race::class);
        $activeRaces = $raceRepository->countActiveRaces();
        if ($activeRaces >= 3) {
            $msg = "There are already 3 active races!";
        } else {

            $horseRepository = $this->getDoctrine()->getRepository(Horse::class);
            $horsesForRace = $horseRepository->getHorsesForRace();

            /**
             * ToDo
             */

            $race = new Race();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($race);
            $entityManager->flush();
            $msg = "Race started successfully!";
        }


        return $this->render('index.html.twig', ['message' => $msg]);
    }
}

