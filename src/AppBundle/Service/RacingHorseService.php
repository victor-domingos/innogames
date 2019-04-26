<?php

namespace AppBundle\Service;

use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use Doctrine\ORM\EntityManagerInterface;

//This class was created to retrieve racing horse data to the controllers and avoid multiple duplicated repository calls
class RacingHorseService{

	private $em;
    private $racingHorseRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->racingHorseRepository = $this->em->getRepository(RacingHorse::class);
    }  

    public function countNumberOfFinishedHorses(Race $race){
        return $this->racingHorseRepository->countNumberOfFinishedHorses($race);
    }

    public function getFinishedRacePodium(Race $race){
        return $this->racingHorseRepository->getFinishedRacePodium($race);
    }

    public function getHorsesInRunningRace(Race $race){
        return $this->racingHorseRepository->getHorsesInRunningRace($race);
    }

    public function getRaceRecord(){
        return $this->racingHorseRepository->getRaceRecord();
    }
}

?>