<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Horse;
use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use AppBundle\Service\HorseService;
use AppBundle\Service\RaceService;
use AppBundle\Service\RacingHorseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends Controller
{
    const RACE_LENGTH = 1500;
    const SECONDS_PER_PROGRESS = 10;

    private $em;
    private $horseService;
    private $raceService;
    private $racingHorseService;

    public function __construct(EntityManagerInterface $em, 
        HorseService $horseService, 
        RaceService $raceService, 
        RacingHorseService $racingHorseService)
    {
        $this->em = $em;
        $this->horseService = $horseService;
        $this->raceService = $raceService;
        $this->racingHorseService = $racingHorseService;
    }

    /**
     * @Route("/create-race", name="create_race")
     */
    public function createRaceAction() {

        if ($this->raceService->countRunningRaces() >= 3) {
            $msg = "There are already 3 running races!";
        } else {
            $this->em->getConnection()->beginTransaction();
            try{
                $horsesForRace = $this->horseService->getHorsesForRace();

                $race = new Race();
                $this->em->persist($race);

                $i = 0;
                foreach ($horsesForRace as $horse){
                    //Creates the racing horse register and set horse as running
                    $this->em->persist(new RacingHorse($race, $horse, 0));

                    $horse->setRunning(1);
                    $this->em->merge($horse);
                    //Escape foreach after selecting the 8th horse
                    if (++$i >= 8) break;
                }

                $this->em->flush();
                $this->em->getConnection()->commit();
                $msg = "Race started successfully!";
            } catch (Exception $e){
                $this->em->getConection()->rollBack();
                throw $e;
            }
        }
        return $this->forward('AppBundle\Controller\MainController::indexAction', ['createRaceMsg' => $msg]);
    }

    /**
     * @Route("/progress-race", name="progress_race")
     */
    public function progressRaceAction() {
        if ($this->raceService->countRunningRaces() == 0) {
            $msg = "There is no running race to progress!";
        } else {
            $this->em->getConnection()->beginTransaction();
            try{

                //Using a variable to get how many horses have finished within that progress,
                //since the number retrieved in the query that checks whether the race has finished 
                //is not fully updated due to the transaction
                $horsesThatFinishedInThisProgress = 0;
                $runningRaces = $this->raceService->getRunningRaces();
                foreach ($runningRaces as $race){

                    //Retrieves the racing horses data for that race
                    $racingHorses = $this->racingHorseService->getHorsesInRunningRace($race);
                    foreach ($racingHorses as $racingHorse) {

                        //Only progress the horses that still have to finish the race
                        //Method returns the number of horses that had finished the race, 
                        //but are yet to be committed in the transaction
                        if ($racingHorse->getDistanceCovered() < constant('self::RACE_LENGTH')){
                            $horsesThatFinishedInThisProgress = $this->progress($racingHorse);
                        }
                    }
                    //Number of horses that finished in this progress will be added 
                    //to the number already committed in the database
                    $this->updateRace($race, $racingHorses, $horsesThatFinishedInThisProgress);
                }

                $this->em->flush();
                $this->em->getConnection()->commit();
                $msg = "Race(s) progressed successfully!";
            } catch (Exception $e){
                $this->em->getConection()->rollBack();
                throw $e;
            }
        }

        return $this->forward('AppBundle\Controller\MainController::indexAction', ['progressRaceMsg' => $msg]);
    }

    //Method to process the progress of each horse of the race, updating his data regarding distance covered and finishing time
    private function progress(RacingHorse $racingHorse){
        $horsesThatFinishedInThisProgress = 0;

        $horse = $racingHorse->getHorse();
        $race = $racingHorse->getRace();
        $startingPoint = $racingHorse->getDistanceCovered();

        //If horse already has passed checkpoint (when the horse becomes slower),
        //the next "checkpoint" would be the finish line
        $checkpoint = $startingPoint < $horse->getCheckpoint() ? $horse->getCheckpoint() : constant('self::RACE_LENGTH');
        
        $startingSpeed = $horse->getStartingSpeed($startingPoint);
        $estimatedDistanceRunned = constant('self::SECONDS_PER_PROGRESS') * $startingSpeed;
        $estimatedFinalPoint = $startingPoint + $estimatedDistanceRunned;

        //The horse will have a reduced speed after the checkpoint, so it must be calculated 
        //how much of the distance is covered with reduced speed.
        //If the checkpoint is the finish line, then it will be calculated when the horse has reached it within that progress
        if ($startingPoint < $checkpoint && $estimatedFinalPoint >= $checkpoint) {

            //Checking if the checkpoint is the endurance limit or the race finish to retrieve the correct information needed
            if ($checkpoint == constant('self::RACE_LENGTH')) {

                //Horse has reached the finishing line, so distance covered is the race length
                $racingHorse->setDistanceCovered($checkpoint);

                //The finishing time will be the race's time elapsed (as it still doesn't have the seconds per progress added)
                //plus the time needed to reach the goal in that progress
                $racingHorse->setTimeInSeconds(
                    $race->getTimeElapsed() + $horse->getTimeToCheckpoint(
                        $checkpoint, constant('self::SECONDS_PER_PROGRESS'), $startingPoint, $estimatedFinalPoint
                    ));

                $horsesThatFinishedInThisProgress++;
            } else {
                $racingHorse->setDistanceCovered($horse->calculateFinalDistanceAfterCheckpoint(
                    constant('self::SECONDS_PER_PROGRESS'), $startingPoint, $estimatedFinalPoint
                ));
            }

        //Horse has not reached any checkpoint, so simply adds the estimated distance 
        //as it runs with an uniform speed the whole progress time
        } else {
            $racingHorse->setDistanceCovered($estimatedFinalPoint);
        }

        $this->em->merge($racingHorse);

        return $horsesThatFinishedInThisProgress;
    }

    //Method to update race data according to race finish or not
    private function updateRace(Race $race, $racingHorses, $horsesThatFinishedInThisProgress){
        $finishedHorses = $horsesThatFinishedInThisProgress +
            $this->racingHorseService->countNumberOfFinishedHorses($race);

        //All horses have finished, so race is finished as well
        if ($finishedHorses == 8){
            $race->finishRace();
            $this->em->merge($race);

            //Update each of the horse's running condition to false
            foreach ($racingHorses as $racingHorse) {
                $horse = $racingHorse->getHorse();
                $horse->setRunning(false);
                $this->em->merge($horse);
            }

        //Race still running, so we the time elapsed is updated after every horse had progressed
        } else {
            $race->setTimeElapsed($race->getTimeElapsed() + constant('self::SECONDS_PER_PROGRESS'));
        }

        $this->em->merge($race);
    }
}
?>