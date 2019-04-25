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

        if ($this->countRunningRaces() >= 3) {
            $msg = "There are already 3 running races!";
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{
                $horsesForRace = $this->getDoctrine()->getRepository(Horse::class)->getHorsesForRace();

                $race = new Race();
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
        return $this->forward('AppBundle\Controller\MainController::indexAction', ['createRaceMsg' => $msg]);
    }

    /**
     * @Route("/progress-race", name="progress_race")
     */
    public function progressRaceAction() {
        if ($this->countRunningRaces() == 0) {
            $msg = "There is no running race to progress!";
        } else {
            $runningRaces = $this->getRunningRaces();
            foreach ($runningRaces as $race){
                $racingHorses = $this->getDoctrine()->getRepository(RacingHorse::class)->getHorsesInRunningRace($race);
                foreach ($racingHorses as $racingHorse) {
                    $this->progress($racingHorse);
                }
            }


            $msg = "Race(s) progressed successfully!";
        }
        return $this->forward('AppBundle\Controller\MainController::indexAction', ['progressRaceMsg' => $msg]);
    }

    private function progress(RacingHorse $racingHorse){
        
    }

    private function getRunningRaces(){
        return $this->getDoctrine()->getRepository(Race::class)->getRunningRaces();
    }

    private function countRunningRaces(){
        return $this->getDoctrine()->getRepository(Race::class)->countRunningRaces();
    }
}
?>