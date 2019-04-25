<?php

namespace AppBundle\Controller;

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

        $repository = $this->getDoctrine()->getRepository(Race::class);
        $activeRaces = $repository->countActiveRaces();
        if ($activeRaces >= 3) {
            $msg = "There are already 3 active races!";
        } else {
            $race = new Race();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($race);
            $entityManager->flush();
            $msg = "Race started successfully!";
        }


        return $this->render('index.html.twig', ['message' => $msg]);
    }
}

