<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
*@ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
*@ORM\Table(name="race")
*/

class Race {
	/**
	*
	* @ORM\Id
   	* @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\Column(name="time_elapsed", type="integer")
     */
    private $timeElapsed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $finished;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RacingHorse", mappedBy="race")
     */
    private $racingHorseRaces;

    public function __construct()
    {
        $this->racingHorseRaces = new ArrayCollection();
    }

    public function getId() {
    	return $this->id;
    }

    public function setId($id) {
    	$this->id = $id;
    }

    public function getTimeElapsed(){
        return $this->timeElapsed;
    }

    public function setTimeElapsed($timeElapsed){
        $this->timeElapsed = $timeElapsed;
    }

    public function getFinished(){
        return $this->finished;
    }

    public function setFinished($finished){
        $this->finished = $finished;
    }

    public function getRacingHorseRaces(){
        return $this->racingHorseRaces;
    }
}
?>