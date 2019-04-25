<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/index", name="homepage")
     */
    public function indexAction($createRaceMsg = null, $progressRaceMsg = null) {
        /*
        //Getting the active races to show on the index page
        $runningRaces = $this->getRunningRaces();
        foreach ($runningRaces as $race){
            $activeRace = $this->getRaceData($race);
        }
*/
        return $this->render('index.html.twig', [
            'createRaceMsg' => $createRaceMsg,
            'progressRaceMsg' => $progressRaceMsg
        ]);
    }

    private function getRunningRaces(){
        return $this->getDoctrine()->getRepository(Race::class)->getRunningRaces();
    }

    private function getRaceData(Race $race){
        return $this->getDoctrine()->getRepository(RacingHorse::class)->getHorsesInRunningRace($race);
    }
}
?>