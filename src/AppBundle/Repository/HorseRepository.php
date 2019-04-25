<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Horse;
use AppBundle\Entity\Race;
use AppBundle\Entity\RacingHorse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class HorseRepository extends EntityRepository {

    public function getHorsesForRace(){

        $expr = $this->_em->getExpressionBuilder();
        $subQueryRace = $this->_em->createQueryBuilder()
            ->select('r.id')
            ->from(Race::class, 'r')
            ->where('r.finished is null');

        $subQueryRacingHorse = $this->_em->createQueryBuilder()
            ->select('rh')
            ->from(RacingHorse::class, 'rh')
            ->where($expr->in('rh.race', $subQueryRace->getDQL()));

        $query = $this->_em->createQueryBuilder()
            ->select('h')
            ->from(Horse::class, 'h')
            ->where($expr->notIn('h.id', $subQueryRacingHorse->getDQL()));

        //The idea was to get a random array of 8 horses, but Doctrine doesn't support the "order by RAND()" and the extensions didn't work properly
        $result = $query->getQuery()->getResult();
        shuffle($result);
        return $result;
    }
}

?>