<?php

namespace AppBundle\Service;

use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use Doctrine\ORM\EntityManagerInterface;

class RaceDataService{

    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function countRunningRaces(){
        return $this->em->getRepository(Race::class)->countRunningRaces();
    }

    public function runningRaceData(Race $race){
        return $this->em->getRepository(RacingHorse::class)->getHorsesInRunningRace($race);
    }
}
?>