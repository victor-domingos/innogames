<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use AppBundle\Service\RaceDataService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    private $raceDataService;

    public function __construct(RaceDataService $raceDataService)
    {
        $this->raceDataService = $raceDataService;
    }

    /**
     * @Route("/index", name="homepage")
     */
    public function indexAction($createRaceMsg = null, $progressRaceMsg = null) {
        $countRunningRaces = $this->raceDataService->countRunningRaces();

        //Creating an array of variables that will be passed to the index.html.twig
        //If something is not appliable (e.g. no running races), then no variable would be passed
        $viewVariables = [
            'createRaceMsg' => $createRaceMsg,
            'progressRaceMsg' => $progressRaceMsg,
            'countRunningRaces' => $countRunningRaces
        ];

        //Running Races
        if ($countRunningRaces > 0){
            $races = array();
            $runningRaces = $this->getRunningRaces();
            //Get the data from each running race and add to the array of variables to show in "real time" in the index page
            foreach ($runningRaces as $race){
                array_push($races, $this->raceDataService->runningRaceData($race));
            }
            $viewVariables['races'] = $races;
        }

        //Last 5 Finished Races
        $finishedRaces = array();
        $lastFiveFinishedRaces = $this->raceDataService->getLastFiveFinishedRaces();
        if (sizeof($lastFiveFinishedRaces) > 0){
            //Retrieving the top-3 data from HorseRacing to be able to retrieve information from Horse
            foreach($lastFiveFinishedRaces as $finishedRace){
                array_push($finishedRaces, $this->raceDataService->getFinishedRacePodium($finishedRace));
            }
            $viewVariables['finishedRaces'] = $finishedRaces;
        }

        //Race Record
        $raceRecord = $this->raceDataService->getRaceRecord();
        if ($raceRecord != null){
            $viewVariables['raceRecord'] = $raceRecord;
        }

        return $this->render('index.html.twig', $viewVariables);
    }

    private function getRunningRaces()
    {
        return $this->getDoctrine()->getRepository(Race::class)->getRunningRaces();
    }
}
?>