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
			->orderBy('rh.timeInSeconds', 'DESC')
			->orderBy('rh.distanceCovered', 'DESC')
			->getQuery()
			->getResult();
	}
}

?>