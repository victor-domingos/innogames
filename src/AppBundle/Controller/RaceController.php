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
    const RACE_LENGTH = 1500;
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
                    //ToDo: Check constructor
                    $racingHorse = new RacingHorse();
                    $racingHorse->setRace($race);
                    $racingHorse->setHorse($horse);
                    $racingHorse->setDistanceCovered(0);
                    $em->persist($racingHorse);

                    $horse->setRunning(1);
                    $em->merge($horse);
                    //Breaks after selecting the 8th horse
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
                        //Only progress the horses that still have to finish the race
                        if ($racingHorse->getDistanceCovered() < constant('self::RACE_LENGTH')){
                            $this->progress($racingHorse);
                        }
                    }
                    //Update the time elapsed of the race after progressing each of the horses
                    $race->setTimeElapsed($race->getTimeElapsed() + constant('self::SECONDS_PER_PROGRESS'));
                    $em->merge($race);
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

    //Method to process the progress of each horse of the race, updating his data regarding distance covered and finishing time
    private function progress(RacingHorse $racingHorse){
        $em = $this->getDoctrine()->getManager();
        $horse = $racingHorse->getHorse();
        $race = $racingHorse->getRace();
        $startingPoint = $racingHorse->getDistanceCovered();

        //If horse already has passed checkpoint, the next "checkpoint" would be the finish line
        $checkpoint = $startingPoint < $horse->getCheckpoint() ? $horse->getCheckpoint() : constant('self::RACE_LENGTH');
        
        $startingSpeed = $horse->getStartingSpeed($startingPoint);
        $estimatedDistance = constant('self::SECONDS_PER_PROGRESS') * $startingSpeed;
        $estimatedFinalPoint = $startingPoint + $estimatedDistance;

        //If horse passes the checkpoint, the horse will have a reduced speed after the checkpoint, so it must be calculated 
        //how much of the distance is covered with the reduced speed. If the checkpoint is the finish line, then it will be 
        //calculated when the horse has reached it within that progress
        if ($startingPoint < $checkpoint && $estimatedFinalPoint >= $checkpoint) {
            //Checking whether the checkpoint is the finishing line or not to retrieve the correct piece of information
            if ($checkpoint == constant('self::RACE_LENGTH')) {
                $racingHorse->setDistanceCovered($checkpoint);
                //The finishing time will be the race's time elapsed (as it still doesn't have the seconds per progress added
                //plus the time needed to reach the goal in that progress
                $racingHorse->setTimeInSeconds(
                    $race->getTimeElapsed() + $horse->getTimeToCheckpoint(
                        $checkpoint, constant('self::SECONDS_PER_PROGRESS'), $startingPoint, $estimatedFinalPoint
                    ));
            } else {
                $racingHorse->setDistanceCovered($horse->calculateFinalDistanceAfterCheckpoint(
                    constant('self::SECONDS_PER_PROGRESS'), $startingPoint, $estimatedFinalPoint
                ));
            }

        //Horse has not reached any checkpoint, so it simply adds the estimated distance as it will run with an uniform speed and in the full time
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