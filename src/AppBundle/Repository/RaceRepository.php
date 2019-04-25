<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RaceRepository extends EntityRepository {

    public function countActiveRaces(){
        return $this->createQueryBuilder('r')
            ->where('r.finished is null')
            ->select('COUNT(r.id) as activeRaces')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

?>