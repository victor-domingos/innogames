<?php

namespace AppBundle\Service;

use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;

//This class was created to retrieve race data to the controllers and avoid multiple duplicated repository calls
class RaceService{

    private $em;
    private $raceRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->raceRepository = $this->em->getRepository(Race::class);
    }

    public function countRunningRaces(){
        return $this->raceRepository->countRunningRaces();
    }

    public function getLastFiveFinishedRaces(){
        return $this->raceRepository->getLastFiveFinishedRaces();
    }

    public function getRunningRaces(){
        return $this->raceRepository->getRunningRaces();
    }
    
}
?>