<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RaceRepository extends EntityRepository {

    public function countRunningRaces(){
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as activeRaces')
            ->where('r.finishedTime is null')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRunningRaces(){
        return $this->createQueryBuilder('r')
            ->where('r.finishedTime is null')
            ->getQuery()
            ->getResult();
    }

    public function getLastFiveFinishedRaces(){
        return $this->createQueryBuilder('r')
            ->where('r.finishedTime is not null')
            ->orderBy('r.finishedTime', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}

?>