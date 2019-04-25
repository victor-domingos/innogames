<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Horse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class HorseRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horse::class);
    }

}

?>