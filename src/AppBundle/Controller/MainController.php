<?php

namespace AppBundle\Controller;

use AppBundle\Service\RaceService;
use AppBundle\Service\RacingHorseService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    private $raceService;
    private $racingHorseService;

    public function __construct(RaceService $raceService, RacingHorseService $racingHorseService)
    {
        $this->raceService = $raceService;
        $this->racingHorseService = $racingHorseService;
    }

    /**
     * @Route("/index", name="homepage")
     */
    public function indexAction($createRaceMsg = null, $progressRaceMsg = null) {
        //Creating an array of variables that will be passed to the index.html.twig
        $viewVariables = [
            'createRaceMsg' => $createRaceMsg,
            'progressRaceMsg' => $progressRaceMsg
        ];

        //Running Races
        $viewVariables['races'] = $this->setRunningRaces();

        //Last 5 Finished Races
        $viewVariables['finishedRaces'] = $this->setFinishedRaces();

        //Race Record
        $viewVariables['raceRecord'] = $this->setRaceRecord();

        return $this->render('index.html.twig', $viewVariables);
    }

    private function setRunningRaces(){
        if ($this->raceService->countRunningRaces() > 0){
            $races = array();
            $runningRaces = $this->raceService->getRunningRaces();
            //Get the data from each running race and add to the array of variables to show in "real time" in the index page
            foreach ($runningRaces as $race){
                array_push($races, $this->racingHorseService->getHorsesInRunningRace($race));
            }
            return $races;
        }
    }

    private function setFinishedRaces(){
        $finishedRaces = array();
        $lastFiveFinishedRaces = $this->raceService->getLastFiveFinishedRaces();
        if (sizeof($lastFiveFinishedRaces) > 0){
            //Retrieving the top-3 data from HorseRacing to be able to retrieve information from Horse
            foreach($lastFiveFinishedRaces as $finishedRace){
                array_push($finishedRaces, $this->racingHorseService->getFinishedRacePodium($finishedRace));
            }
            return $finishedRaces;
        }
    }

    private function setRaceRecord(){
        $raceRecord = $this->racingHorseService->getRaceRecord();
        if ($raceRecord != null){
             return $raceRecord[0];
        }
    }
}
?>