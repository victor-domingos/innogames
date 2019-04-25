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

    const SECONDS_PER_PROGRESS = 10;

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
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{
                $runningRaces = $this->getRunningRaces();
                foreach ($runningRaces as $race){
                    $racingHorses = $this->getDoctrine()->getRepository(RacingHorse::class)->getHorsesInRunningRace($race);
                    foreach ($racingHorses as $racingHorse) {
                        $this->progress($racingHorse);
                    }
                }

                $em->flush();
                $em->getConnection()->commit();
                $msg = "Race(s) progressed successfully!";
            } catch (Exception $e){
                $em->getConection()->rollBack();
                throw $e;
            }
        }
        return $this->forward('AppBundle\Controller\MainController::indexAction', ['progressRaceMsg' => $msg]);
    }

    private function progress(RacingHorse $racingHorse){
        $em = $this->getDoctrine()->getManager();
        $horse = $racingHorse->getHorse();
        $checkpoint = $horse->getCheckpoint();
        $startingPoint = $racingHorse->getDistanceCovered();
        $startingSpeed = $horse->getStartingSpeed($startingPoint);
        $estimatedDistance = constant('self::SECONDS_PER_PROGRESS') * $startingSpeed;
        $estimatedFinalPoint = $startingPoint + $estimatedDistance;
        //If horse passes the checkpoint, the horse will have a reduced speed after the checkpoint, so it must be calculated 
        //how much of the distance is covered with the reduced speed
        if ($startingPoint < $checkpoint && $estimatedFinalPoint >= $checkpoint) {
            $racingHorse->setDistanceCovered($horse->calculateFinalDistanceAfterCheckpoint(
                constant('self::SECONDS_PER_PROGRESS'), $startingPoint, $estimatedFinalPoint
            ));
        //Horse has not reached the point of slowdown, so it simply adds the estimated distance
        } else {
            $racingHorse->setDistanceCovered($estimatedFinalPoint);
        }
        $em->merge($racingHorse);

    }

    private function getRunningRaces(){
        return $this->getDoctrine()->getRepository(Race::class)->getRunningRaces();
    }

    private function countRunningRaces(){
        return $this->getDoctrine()->getRepository(Race::class)->countRunningRaces();
    }
}
?>