<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RaceRepository extends EntityRepository {

    public function countActiveRaces(){
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as activeRaces')
            ->where('r.finishedTime is null')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getActiveRaces(){
        return $this->createQueryBuilder('r')
            ->where('r.finishedTime is null')
            ->getQuery()
            ->getResult();
    }
}

?>