<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Horse;
use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class HorseRepository extends EntityRepository {

    public function getHorsesForRace(){
        $query = $this->createQueryBuilder('h')
            ->select('h')
            ->where('h.running = 0');

        //The idea was to get a random array of 8 horses, but Doctrine doesn't support the "order by RAND()" and the extensions didn't work properly
        $result = $query->getQuery()->getResult();
        shuffle($result);
        return $result;
    }
}

?>