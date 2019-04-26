<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Race;
use Doctrine\ORM\EntityRepository;

class RacingHorseRepository extends EntityRepository {

	public function getHorsesInRunningRace(Race $race){
		return $this->createQueryBuilder('rh')
			->select('rh')
			->where('rh.race = :race')
			->setParameter('race', $race)
			->addOrderBy('rh.distanceCovered', 'DESC')
            ->addOrderBy('rh.timeInSeconds', 'ASC')
			->getQuery()
			->getResult();
	}

	public function countNumberOfFinishedHorses(Race $race){
        return $this->createQueryBuilder('rh')
            ->select('COUNT(rh.id) as finishedHorses')
            ->where('rh.race = :race')
            ->andWhere('rh.timeInSeconds is not null')
            ->setParameter('race', $race)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFinishedRacePodium(Race $race){
        return $this->createQueryBuilder('rh')
            ->select('rh')
            ->where('rh.race = :race')
            ->setParameter('race', $race)
            ->addOrderBy('rh.distanceCovered', 'DESC')
            ->addOrderBy('rh.timeInSeconds', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function getRaceRecord(){
        return $this->createQueryBuilder('rh')
            ->select('rh')
            ->andWhere('rh.timeInSeconds is not null')
            ->addOrderBy('rh.timeInSeconds', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}

?>