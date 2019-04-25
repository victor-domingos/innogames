<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class RaceRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

}

?>