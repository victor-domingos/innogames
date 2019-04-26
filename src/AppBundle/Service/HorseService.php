<?php

namespace AppBundle\Service;

use AppBundle\Entity\Horse;
use Doctrine\ORM\EntityManagerInterface;

//This class was created to retrieve horse data to the controllers and avoid multiple duplicated repository calls
class HorseService{

	private $em;
    private $horseRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->horseRepository = $this->em->getRepository(Horse::class);
    }

    public function getHorsesForRace(){
    	return $this->horseRepository->getHorsesForRace();
    }

}

?>