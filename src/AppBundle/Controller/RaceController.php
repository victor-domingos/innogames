<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Horse;
use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends Controller
{
    /**
     * @Route("/create-race", name="create_race")
     */
    public function createRaceAction() {

        $raceRepository = $this->getDoctrine()->getRepository(Race::class);
        $activeRaces = $raceRepository->countActiveRaces();
        if ($activeRaces >= 3) {
            $msg = "There are already 3 active races!";
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{
                $horsesForRace = $this->getDoctrine()->getRepository(Horse::class)->getHorsesForRace();

                $race = new Race();
                $race->setTimeElapsed(0);
                $em->persist($race);

                $i = 0;
                foreach ($horsesForRace as $horse){
                    $racingHorse = new RacingHorse();
                    $racingHorse->setRace($race);
                    $racingHorse->setHorse($horse);
                    $racingHorse->setDistanceCovered(0);
                    $em->persist($racingHorse);

                    $horse->setRunning(1);
                    $em->merge($horse);
                    if (++$i >= 8) break;
                }

                $em->flush();
                $em->getConnection()->commit();
                $msg = "Race started successfully!";
            } catch (Exception $e){
                $em->getConection()->rollBack();
                throw $e;
            }
        }
        return $this->render('index.html.twig', ['create_race_msg' => $msg]);
    }

    private function getHorsesForRace(){
        return $this->getDoctrine()->getRepository(Horse::class)->getHorsesForRace();
    }
}
?>